<?php

namespace app\index\controller\cms;

use Throwable;
use app\index\validate\Account as AccountValidate;
use app\common\facade\Token;
use ba\Captcha;
use ba\Random;
use app\common\model\UserScoreLog;
use app\common\model\UserMoneyLog;
use app\common\model\User;
use think\facade\Validate;

class Account extends Base
{
    protected array $noNeedLogin = ['retrievePassword'];


    public function initialize(): void
    {
        parent::initialize();
        $this->userInitialize();
    }

    public function overview(): string
    {
        return $this->view->fetch('user/account/overview');
    }

    /**
     * 会员资料
     * @throws Throwable
     */
    public function profile(): string
    {
        if ($this->request->isPost()) {
            $data = $this->request->only(['id', 'avatar', 'username', 'nickname', 'gender', 'birthday', 'motto']);
            if (!isset($data['birthday'])) {
                $data['birthday'] = null;
            }

            try {
                $validate = new AccountValidate();
                $validate->scene('edit')->check($data);
            } catch (Throwable $e) {
                $this->error($e->getMessage());
            }

            $model = $this->auth->getUser();
            $model->startTrans();
            try {
                $model->save($data);
                $model->commit();
            } catch (Throwable $e) {
                $model->rollback();
                $this->error($e->getMessage());
            }

            $this->success(__('Data updated successfully~'));
        }
        $this->view->assign([
            'accountVerificationType' => json_encode(get_account_verification_type())
        ]);
        return $this->view->fetch('user/account/profile');
    }

    /**
     * 通过手机号或邮箱验证账户
     * 此处检查的验证码是通过 api/Ems或api/Sms发送的
     * 验证成功后，向前端返回一个 email-pass Token或着 mobile-pass Token
     * 在 changBind 方法中，通过 pass Token来确定用户已经通过了账户验证（用户未绑定邮箱/手机时通过账户密码验证）
     * @throws Throwable
     */
    public function verification(): void
    {
        $captcha = new Captcha();
        $params  = $this->request->only(['type', 'captcha']);
        if ($captcha->check($params['captcha'], ($params['type'] == 'email' ? $this->auth->email : $this->auth->mobile) . "user_{$params['type']}_verify")) {
            $uuid = Random::uuid();
            Token::set($uuid, $params['type'] . '-pass', $this->auth->id, 600);
            $this->success('', '', [
                'type'                     => $params['type'],
                'accountVerificationToken' => $uuid,
            ]);
        }
        $this->error(__('Please enter the correct verification code'));
    }

    /**
     * 修改绑定信息（手机号、邮箱）
     * 通过 pass Token来确定用户已经通过了账户验证，也就是以上的 verification 方法，同时用户未绑定邮箱/手机时通过账户密码验证
     * @throws Throwable
     */
    public function changeBind(): void
    {
        $captcha = new Captcha();
        $params  = $this->request->only(['type', 'captcha', 'email', 'mobile', 'accountVerificationToken', 'password']);
        $user    = $this->auth->getUser();

        if ($user[$params['type']]) {
            if (!Token::check($params['accountVerificationToken'], $params['type'] . '-pass', $user->id)) {
                $this->error(__('You need to verify your account before modifying the binding information'));
            }
        } elseif (!isset($params['password']) || $user->password != encrypt_password($params['password'], $user->salt)) {
            $this->error(__('Password error'));
        }

        // 检查验证码
        if ($captcha->check($params['captcha'], $params[$params['type']] . "user_change_{$params['type']}")) {
            if ($params['type'] == 'email') {
                $validate = Validate::rule(['email' => 'require|email|unique:user'])->message([
                    'email.require' => 'email format error',
                    'email.email'   => 'email format error',
                    'email.unique'  => 'email is occupied',
                ]);
                if (!$validate->check(['email' => $params['email']])) {
                    $this->error(__($validate->getError()));
                }
                $user->email = $params['email'];
            } elseif ($params['type'] == 'mobile') {
                $validate = Validate::rule(['mobile' => 'require|mobile|unique:user'])->message([
                    'mobile.require' => 'mobile format error',
                    'mobile.mobile'  => 'mobile format error',
                    'mobile.unique'  => 'mobile is occupied',
                ]);
                if (!$validate->check(['mobile' => $params['mobile']])) {
                    $this->error(__($validate->getError()));
                }
                $user->mobile = $params['mobile'];
            }
            Token::delete($params['accountVerificationToken']);
            $user->save();
            $this->success();
        }
        $this->error(__('Please enter the correct verification code'));


    }

    /**
     * 修改密码
     * @throws Throwable
     */
    public function changePassword(): string
    {
        if ($this->request->isPost()) {
            $params = $this->request->only(['oldPassword', 'newPassword']);

            if (!$this->auth->checkPassword($params['oldPassword'])) {
                $this->error(__('Old password error'));
            }

            $model = $this->auth->getUser();
            $model->startTrans();
            try {
                $validate = new AccountValidate();
                $validate->scene('changePassword')->check(['password' => $params['newPassword']]);
                $model->resetPassword($this->auth->id, $params['newPassword']);
                $model->commit();
            } catch (Throwable $e) {
                $model->rollback();
                $this->error($e->getMessage());
            }

            $this->auth->logout();
            $this->success(__('Password has been changed, please login again~'));
        }
        return $this->view->fetch('user/account/changePassword');
    }

    /**
     * 积分日志
     * @throws Throwable
     */
    public function integral(): string
    {
        if ($this->request->isPost()) {
            $limit         = $this->request->request('limit');
            $integralModel = new UserScoreLog();
            $res           = $integralModel->where('user_id', $this->auth->id)
                ->order('create_time desc')
                ->paginate($limit);

            $this->success('', '', [
                'list'  => $res->items(),
                'total' => $res->total(),
            ]);
        }
        return $this->view->fetch('user/account/integral');
    }

    /**
     * 余额日志
     * @throws Throwable
     */
    public function balance(): string
    {
        if ($this->request->isPost()) {
            $limit      = $this->request->request('limit');
            $moneyModel = new UserMoneyLog();
            $res        = $moneyModel->where('user_id', $this->auth->id)
                ->order('create_time desc')
                ->paginate($limit);

            $this->success('', '', [
                'list'  => $res->items(),
                'total' => $res->total(),
                ]);
        }
        return $this->view->fetch('user/account/balance');
    }

    /**
     * 找回密码
     * @throws Throwable
     */
    public function retrievePassword(): void
    {
        $params = $this->request->only(['type', 'account', 'captcha', 'password']);
        try {
            $validate = new AccountValidate();
            $validate->scene('retrievePassword')->check($params);
        } catch (Throwable $e) {
            $this->error($e->getMessage());
        }

        if ($params['type'] == 'email') {
            $user = User::where('email', $params['account'])->find();
        } else {
            $user = User::where('mobile', $params['account'])->find();
        }
        if (!$user) {
            $this->error(__('Account does not exist~'));
        }

        $captchaObj = new Captcha();
        if (!$captchaObj->check($params['captcha'], $params['account'] . 'user_retrieve_pwd')) {
            $this->error(__('Please enter the correct verification code'));
        }

        if ($user->resetPassword($user->id, $params['password'])) {
            $this->success(__('Password has been changed~'));
        } else {
            $this->error(__('Failed to modify password, please try again later~'));
        }
    }
}
