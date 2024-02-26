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
        <el-form label-width="120px" ref="formRef" :model="form">
            <el-form-item
                label="Name"
                prop="name"
                :rules="[
                    {
                        required: true,
                        message: 'Name is required',
                        trigger: 'blur',
                    },
                ]"
            >
                <el-input v-model="form.name" placeholder="" />
                <div v-if="form.errors.name" class="text-red-600">
                    {{ form.errors.name }}
                </div>
            </el-form-item>
            <el-form-item label="Username" prop="username">
                <el-input v-model="form.username" placeholder="" />
                <div v-if="form.errors.username" class="text-red-600">
                    {{ form.errors.username }}
                    <span class="">Username might not have space.</span>
                </div>
            </el-form-item>
            <el-form-item label="Phone" prop="phone">
                <el-input v-model="form.phone" placeholder="" />
                <div v-if="form.errors.phone" class="text-red-600">
                    {{ form.errors.phone }}
                </div>
            </el-form-item>
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
    props: ["show", "title", "data"],
    emits: ["closed"],
    setup(props, context) {
        const state = reactive({
            dialogTitle: "",
            doubleClick: false,
        });

        const formRef = ref();

        const form = useForm({
            name: "",
            phone: "",
            username: "",
            movie: "",
        });

        const submitDialog = (formRef) => {
            formRef.validate((valid) => {
                if (valid) {
                    if (state.dialogTitle === "Create") {
                        state.doubleClick = true;
                        form.post(route("admin.customers.store"), {
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
                            route("admin.customers.update", props.data.id),
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
            form.name = props.data.name ?? "";
            form.phone = props.data.phone ?? "";
            form.username = props.data.username ?? "";
            form.movie = props.data.movie ?? "";
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
