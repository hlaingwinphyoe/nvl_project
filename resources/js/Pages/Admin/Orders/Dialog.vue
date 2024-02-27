<template>
    <el-dialog
        :modelValue="show"
        @update:modelValue="show = $event"
        @close="closeDialog(formRef)"
        @open="openDialog"
        draggable
        :title="dialogTitle"
        :close-on-click-modal="false"
    >
        <el-form
            label-width="120px"
            ref="formRef"
            :model="form"
            label-position="top"
        >
            <div class="grid grid-cols-2 gap-4">
                <el-form-item
                    label="Customer"
                    prop="customer"
                    size="large"
                    :rules="[
                        {
                            required: true,
                            message: 'Customer is required',
                            trigger: 'blur',
                        },
                    ]"
                >
                    <el-select
                        v-model="form.customer"
                        filterable
                        placeholder="Select Customer"
                    >
                        <el-option
                            v-for="item in customers"
                            :key="item.id"
                            :label="item.name"
                            :value="item.id"
                        >
                            <span style="float: left">{{ item.name }}</span>
                            <span
                                style="
                                    float: right;
                                    color: var(--el-text-color-secondary);
                                    font-size: 13px;
                                "
                                >{{ item.username }}</span
                            >
                        </el-option>
                    </el-select>
                    <div v-if="form.errors.customer" class="text-red-600">
                        {{ form.errors.customer }}
                    </div>
                </el-form-item>
                <el-form-item
                    label="Serie"
                    size="large"
                    :rules="[
                        {
                            required: true,
                            message: 'Serie is required',
                            trigger: 'blur',
                        },
                    ]"
                >
                    <el-select
                        v-model="form.series"
                        filterable
                        multiple
                        placeholder="Select Serie"
                    >
                        <el-option
                            v-for="item in series"
                            :key="item.id"
                            :label="item.title"
                            :value="item.id"
                        >
                            <span style="float: left">{{ item.title }}</span>
                            <span
                                style="
                                    float: right;
                                    color: var(--el-text-color-secondary);
                                    font-size: 13px;
                                "
                                >{{ item.price }} Ks</span
                            >
                        </el-option>
                    </el-select>
                    <div v-if="form.errors.serie" class="text-red-600">
                        {{ form.errors.serie }}
                    </div>
                </el-form-item>
            </div>
        </el-form>
        <template #footer>
            <div class="dialog-footer">
                <el-button @click="closeDialog(formRef)">Cancel</el-button>
                <el-button type="primary" @click="submitDialog(formRef)">
                    Save
                </el-button>
            </div>
        </template>
    </el-dialog>
</template>

<script>
import { reactive, ref, toRefs } from "vue";
import { useForm } from "@inertiajs/vue3";
import { ElMessage } from "element-plus";
export default {
    props: ["show", "title", "data", "series", "customers"],
    emits: ["closed"],
    setup(props, context) {
        const state = reactive({
            dialogTitle: "",
            doubleClick: false,
        });

        const formRef = ref();

        const form = useForm({
            customer: "",
            series: [],
        });

        const submitDialog = (formRef) => {
            formRef.validate((valid) => {
                if (valid) {
                    if (state.dialogTitle === "Create") {
                        state.doubleClick = true;
                        form.post(route("admin.orders.store"), {
                            onSuccess: (page) => {
                                state.doubleClick = false;
                                closeDialog(formRef);
                                ElMessage.success(page.props.flash.success);
                            },
                            onError: () => {
                                state.doubleClick = false;
                            },
                        });
                    } else {
                        state.doubleClick = true;
                        form.patch(
                            route("admin.orders.update", props.data.id),
                            {
                                onSuccess: (page) => {
                                    state.doubleClick = false;
                                    closeDialog(formRef);
                                    ElMessage.success(page.props.flash.success);
                                },
                                onError: () => {
                                    state.doubleClick = false;
                                },
                            }
                        );
                    }
                }
            });
        };

        const closeDialog = (formRef) => {
            form.reset();
            formRef.resetFields();
            context.emit("closed");
        };

        const openDialog = () => {
            state.dialogTitle = props.title;
            form.customer = props.data.customer_id;
            form.series = props.data.order_items.map((a) => a.serie_id) ?? "";
        };

        return {
            ...toRefs(state),
            openDialog,
            formRef,
            closeDialog,
            submitDialog,
            form,
        };
    },
};
</script>

<style></style>
