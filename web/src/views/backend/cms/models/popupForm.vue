<template>
    <!-- 对话框表单 -->
    <!-- 建议使用 Prettier 格式化代码 -->
    <!-- el-form 内可以混用 el-form-item、FormItem、ba-input 等输入组件 -->
    <el-dialog
        class="ba-operate-dialog"
        :close-on-click-modal="false"
        :model-value="['Add', 'Edit'].includes(baTable.form.operate!)"
        @close="baTable.toggleForm"
        width="50%"
    >
        <template #header>
            <div class="title" v-drag="['.ba-operate-dialog', '.el-dialog__header']" v-zoom="'.ba-operate-dialog'">
                {{ baTable.form.operate ? t(baTable.form.operate) : '' }}
            </div>
        </template>
        <el-scrollbar v-loading="baTable.form.loading" class="ba-table-form-scrollbar">
            <div
                class="ba-operate-form"
                :class="'ba-' + baTable.form.operate + '-form'"
                :style="config.layout.shrink ? '':'width: calc(100% - ' + baTable.form.labelWidth! / 2 + 'px)'"
            >
                <el-form
                    v-if="!baTable.form.loading"
                    ref="formRef"
                    @submit.prevent=""
                    @keyup.enter="baTable.onSubmit(formRef)"
                    :model="baTable.form.items"
                    :label-position="config.layout.shrink ? 'top' : 'right'"
                    :label-width="baTable.form.labelWidth + 'px'"
                    :rules="rules"
                >
                    <FormItem :label="t('cms.models.name')" type="string" v-model="baTable.form.items!.name" prop="name" :placeholder="t('Please input field', { field: t('cms.models.name') })" />
                    <FormItem :label="t('cms.models.type')" type="select" v-model="baTable.form.items!.type" prop="type" :input-attr="{ content: baTable.form.extend!.typeList }" :placeholder="t('Please select field', { field: t('cms.models.type') })" />
                    <FormItem :label="t('cms.models.urlname')" type="string" v-model="baTable.form.items!.urlname" prop="urlname" :placeholder="t('Please input field', { field: t('cms.models.urlname') })" />
                    <FormItem :label="t('cms.models.listtpl')" type="string" v-model="baTable.form.items!.listtpl" prop="listtpl" :placeholder="t('Please input field', { field: t('cms.models.listtpl') })" />
                    <FormItem :label="t('cms.models.contenttpl')" type="string" v-model="baTable.form.items!.contenttpl" prop="contenttpl" :placeholder="t('Please input field', { field: t('cms.models.contenttpl') })" />
                    <FormItem :label="t('cms.models.status')" type="radio" v-model="baTable.form.items!.status" prop="status" :input-attr="{ content: {'1':'启用','0':'禁用'} }" :placeholder="t('Please select field', { field: t('cms.models.status') })" />
                </el-form>
            </div>
        </el-scrollbar>
        <template #footer>
            <div :style="'width: calc(100% - ' + baTable.form.labelWidth! / 1.8 + 'px)'">
                <el-button @click="baTable.toggleForm()">{{ t('Cancel') }}</el-button>
                <el-button v-blur :loading="baTable.form.submitLoading" @click="baTable.onSubmit(formRef)" type="primary">
                    {{ baTable.form.operateIds && baTable.form.operateIds.length > 1 ? t('Save and edit next item') : t('Save') }}
                </el-button>
            </div>
        </template>
    </el-dialog>
</template>

<script setup lang="ts">
import type {FormInstance, FormItemRule} from 'element-plus'
import {inject, reactive, ref} from 'vue'
import {useI18n} from 'vue-i18n'
import FormItem from '/@/components/formItem/index.vue'
import {useConfig} from '/@/stores/config'
import type baTableClass from '/@/utils/baTable'
import {buildValidatorData} from '/@/utils/validate'

const config = useConfig()
const formRef = ref<FormInstance>()
const baTable = inject('baTable') as baTableClass

const {t} = useI18n()

const rules: Partial<Record<string, FormItemRule[]>> = reactive({
    name: [buildValidatorData({name: 'required', title: t('cms.models.name')})],
    type: [buildValidatorData({name: 'required', title: t('cms.models.type')})],
    create_time: [buildValidatorData({name: 'date', title: t('cms.models.create_time')})],
    update_time: [buildValidatorData({name: 'date', title: t('cms.models.update_time')})],
})
</script>

<style scoped lang="scss"></style>
