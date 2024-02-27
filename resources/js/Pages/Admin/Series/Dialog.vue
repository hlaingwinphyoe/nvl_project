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
                    label="Title"
                    prop="title"
                    size="large"
                    :rules="[
                        {
                            required: true,
                            message: 'Title is required',
                            trigger: 'blur',
                        },
                    ]"
                >
                    <el-input v-model="form.title" placeholder="" />
                    <div v-if="form.errors.title" class="text-red-600">
                        {{ form.errors.title }}
                    </div>
                </el-form-item>
                <el-form-item
                    label="Price"
                    prop="price"
                    size="large"
                    :rules="[
                        {
                            required: true,
                            message: 'Price is required',
                            trigger: 'blur',
                        },
                    ]"
                >
                    <el-input
                        type="number"
                        v-model="form.price"
                        placeholder=""
                    />
                    <div v-if="form.errors.price" class="text-red-600">
                        {{ form.errors.price }}
                    </div>
                </el-form-item>
            </div>
            <el-form-item label="Description" prop="description">
                <el-input
                    type="textarea"
                    v-model="form.description"
                    placeholder=""
                    rows="4"
                />
                <div v-if="form.errors.description" class="text-red-600">
                    {{ form.errors.description }}
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
            title: "",
            description: "",
            price: "",
        });

        const submitDialog = (formRef) => {
            formRef.validate((valid) => {
                if (valid) {
                    if (state.dialogTitle === "Create") {
                        state.doubleClick = true;
                        form.post(route("admin.series.store"), {
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
                            route("admin.series.update", props.data.id),
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
            form.title = props.data.title ?? "";
            form.description = props.data.description ?? "";
            form.price = props.data.price ?? "";
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
