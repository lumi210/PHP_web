<?php

// +----------------------------------------------------------------------
// | BuildAdmin-WEB终端配置
// +----------------------------------------------------------------------

return [
    // npm包管理器
    'npm_package_manager' => 'pnpm',
    // 允许执行的命令
    'commands'            => [
        // 数据库迁移命令
        'migrate'               => [
            'run'        => 'php think migrate:run',
            'rollback'   => 'php think migrate:rollback',
            'breakpoint' => 'php think migrate:breakpoint',
        ],
        // 安装包管理器的命令
        'install'               => [
            'cnpm' => 'npm install cnpm -g --registry=https://registry.npmmirror.com',
            'yarn' => 'npm install -g yarn',
            'pnpm' => 'npm install -g pnpm',
            'ni'   => 'npm install -g @antfu/ni',
        ],
        // 查看版本的命令
        'version'               => [
            'npm'  => 'npm -v',
            'cnpm' => 'cnpm -v',
            'yarn' => 'yarn -v',
            'pnpm' => 'pnpm -v',
            'node' => 'node -v',
        ],
        // 测试命令
        'test'                  => [
            'npm'  => [
                'cwd'     => 'public/npm-install-test',
                'command' => 'npm install',
            ],
            'cnpm' => [
                'cwd'     => 'public/npm-install-test',
                'command' => 'cnpm install',
            ],
            'yarn' => [
                'cwd'     => 'public/npm-install-test',
                'command' => 'yarn install',
            ],
            'pnpm' => [
                'cwd'     => 'public/npm-install-test',
                'command' => 'pnpm install',
            ],
            'ni'   => [
                'cwd'     => 'public/npm-install-test',
                'command' => 'ni install',
            ],
        ],
        // 安装 WEB 依赖包
        'web-install'           => [
            'npm'  => [
                'cwd'     => 'web',
                'command' => 'npm install',
            ],
            'cnpm' => [
                'cwd'     => 'web',
                'command' => 'cnpm install',
            ],
            'yarn' => [
                'cwd'     => 'web',
                'command' => 'yarn install',
            ],
            'pnpm' => [
                'cwd'     => 'web',
                'command' => 'pnpm install',
            ],
            'ni'   => [
                'cwd'     => 'web',
                'command' => 'ni install',
            ],
        ],
        // 安装 Web-Nuxt 依赖包
        'nuxt-install'          => [
            'npm'  => [
                'cwd'     => 'web-nuxt',
                'command' => 'npm install',
            ],
            'cnpm' => [
                'cwd'     => 'web-nuxt',
                'command' => 'cnpm install',
            ],
            'yarn' => [
                'cwd'     => 'web-nuxt',
                'command' => 'yarn install',
            ],
            'pnpm' => [
                'cwd'     => 'web-nuxt',
                'command' => 'pnpm install',
            ],
            'ni'   => [
                'cwd'     => 'web-nuxt',
                'command' => 'ni install',
            ],
        ],
        // 构建 WEB 端
        'web-build'             => [
            'npm'  => [
                'cwd'     => 'web',
                'command' => 'npm run build',
            ],
            'cnpm' => [
                'cwd'     => 'web',
                'command' => 'cnpm run build',
            ],
            'yarn' => [
                'cwd'     => 'web',
                'command' => 'yarn run build',
            ],
            'pnpm' => [
                'cwd'     => 'web',
                'command' => 'pnpm run build',
            ],
            'ni'   => [
                'cwd'     => 'web',
                'command' => 'nr build',
            ],
        ],
        // 设置 NPM 源
        'set-npm-registry'      => [
            'npm'     => 'npm config set registry https://registry.npmjs.org/ && npm config get registry',
            'taobao'  => 'npm config set registry https://registry.npmmirror.com/ && npm config get registry',
            'tencent' => 'npm config set registry https://mirrors.cloud.tencent.com/npm/ && npm config get registry'
        ],
        // 设置 composer 源
        'set-composer-registry' => [
            'composer' => 'composer config --unset repos.packagist',
            'aliyun'   => 'composer config -g repos.packagist composer https://mirrors.aliyun.com/composer/',
            'tencent'  => 'composer config -g repos.packagist composer https://mirrors.cloud.tencent.com/composer/',
            'huawei'   => 'composer config -g repos.packagist composer https://mirrors.huaweicloud.com/repository/php/',
            'kkame'    => 'composer config -g repos.packagist composer https://packagist.kr',
        ],
        'composer'              => [
            'update' => [
                'cwd'     => '',
                'command' => 'composer update --no-interaction',
            ]
        ],
        'ping'                  => [
            'baidu'     => 'ping baidu.com',
            'localhost' => 'ping 127.0.0.1 -n 6',
        ]
    ],
];
