<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="flex items-center justify-between mb-4">
                            <el-button type="primary" @click="addNew">
                                <el-icon class="mr-1">
                                    <Plus />
                                </el-icon>
                                Add New
                            </el-button>
                            <div>
                                <el-input
                                    placeholder="Search customer..."
                                    v-model="param.search"
                                    size="large"
                                />
                            </div>
                        </div>

                        <div
                            class="relative overflow-x-auto"
                            v-loading="isLoading"
                            element-loading-text="Loading..."
                            element-loading-background="rgba(229, 231, 235, 0.7)"
                        >
                            <el-table
                                :data="customers.data"
                                :default-sort="{
                                    prop: 'id',
                                    order: 'descending',
                                }"
                                table-layout="fixed"
                            >
                                <el-table-column
                                    prop="id"
                                    label="Sr."
                                    sortable
                                    width="150"
                                />
                                <el-table-column
                                    prop="name"
                                    label="Name"
                                    sortable
                                    align="center"
                                />
                                <el-table-column
                                    prop="username"
                                    label="Username"
                                    align="center"
                                />
                                <el-table-column
                                    prop="phone"
                                    label="Phone"
                                    align="center"
                                />
                                <el-table-column
                                    prop="created_at"
                                    label="Joined At"
                                    sortable
                                    align="center"
                                />

                                <el-table-column label="Actions" align="center">
                                    <template #default="scope">
                                        <el-tooltip
                                            class="box-item"
                                            content="Edit"
                                            placement="top"
                                        >
                                            <el-button
                                                type="warning"
                                                style="margin-bottom: 5px"
                                                circle
                                                @click="handleEdit(scope.row)"
                                            >
                                                <el-icon><Edit /></el-icon>
                                            </el-button>
                                        </el-tooltip>
                                        <!-- <el-tooltip
                                            class="box-item"
                                            content="Delete"
                                            placement="top"
                                        >
                                            <el-button
                                                type="danger"
                                                circle
                                                style="margin-bottom: 5px"
                                                @click="
                                                    deleteHandler(scope.row.id)
                                                "
                                            >
                                                <el-icon><Delete /></el-icon>
                                            </el-button>
                                        </el-tooltip> -->

                                        <el-tooltip
                                            v-if="scope.row.is_ban == 0"
                                            class="box-item"
                                            content="Ban"
                                            placement="top"
                                        >
                                            <el-button
                                                type="danger"
                                                circle
                                                style="margin-bottom: 5px"
                                                @click="handleBan(scope.row.id)"
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    class="icon icon-tabler icon-tabler-ban"
                                                    width="16"
                                                    height="16"
                                                    viewBox="0 0 24 24"
                                                    stroke-width="1.5"
                                                    stroke="currentColor"
                                                    fill="none"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                >
                                                    <path
                                                        stroke="none"
                                                        d="M0 0h24v24H0z"
                                                        fill="none"
                                                    />
                                                    <path
                                                        d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"
                                                    />
                                                    <path
                                                        d="M5.7 5.7l12.6 12.6"
                                                    />
                                                </svg>
                                            </el-button>
                                        </el-tooltip>
                                        <el-tooltip
                                            v-else
                                            class="box-item"
                                            content="UnBan"
                                            placement="top"
                                        >
                                            <el-button
                                                type="info"
                                                circle
                                                style="margin-bottom: 5px"
                                                @click="handleBan(scope.row.id)"
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    class="icon icon-tabler icon-tabler-arrow-back"
                                                    width="16"
                                                    height="16"
                                                    viewBox="0 0 24 24"
                                                    stroke-width="1.5"
                                                    stroke="currentColor"
                                                    fill="none"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                >
                                                    <path
                                                        stroke="none"
                                                        d="M0 0h24v24H0z"
                                                        fill="none"
                                                    />
                                                    <path
                                                        d="M9 11l-4 4l4 4m-4 -4h11a4 4 0 0 0 0 -8h-1"
                                                    />
                                                </svg>
                                            </el-button>
                                        </el-tooltip>
                                    </template>
                                </el-table-column>
                            </el-table>
                            <div class="my-5 flex items-center justify-center">
                                <el-pagination
                                    @size-change="onSizeChange"
                                    @current-change="onCurrentChange"
                                    :page-size="param.page_size"
                                    :background="true"
                                    :page-sizes="pageList"
                                    :current-page="param.page"
                                    :layout="`total,sizes,prev,pager,next,jumper`"
                                    :total="customers.total"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <Dialog
            :show="showDialog"
            @closed="closeDialog"
            :title="dialog.dialogTitle"
            :data="dialog.dialogData"
        />
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Delete, Edit, Plus } from "@element-plus/icons-vue";
import { Head, router } from "@inertiajs/vue3";
import { reactive, toRefs, watch } from "vue";
import { ElMessage, ElMessageBox } from "element-plus";
import debounce from "lodash.debounce";
import Dialog from "./Dialog.vue";
export default {
    props: ["customers"],
    setup(props) {
        const state = reactive({
            showDialog: false,
            isLoading: false,
            dialog: {
                dialogTitle: "",
                dialogData: {},
            },
            pageList: [10, 20, 60, 80, 100],
            param: {
                page: 1,
                page_size: 10,
                search: "",
            },
        });

        const addNew = () => {
            state.dialog.dialogTitle = "Create";
            state.dialog.dialogData = {};
            state.showDialog = true;
        };

        const handleEdit = (row) => {
            state.dialog.dialogTitle = "Edit";
            state.dialog.dialogData = JSON.parse(JSON.stringify(row));
            state.showDialog = true;
        };

        watch(
            () => state.param.search,
            debounce(() => {
                getData();
            }, 500)
        );

        const onSizeChange = (val) => {
            state.param.page_size = val;
            getData();
        };

        const onCurrentChange = (val) => {
            state.param.page = val;
            getData();
        };

        function getData() {
            state.isLoading = true;
            router.get(route("admin.customers.index"), state.param, {
                preserveScroll: true,
                preserveState: true,
                replace: true,
                onFinish: () => {
                    state.isLoading = false;
                },
            });
        }

        const handleBan = (id) => {
            ElMessageBox.confirm("Are you sure you want to ban?", "Warning", {
                confirmButtonText: "Confirm",
                cancelButtonText: "Cancel",
                type: "warning",
                draggable: true,
                closeOnClickModal: false,
            })
                .then(() => {
                    router.patch(
                        route("admin.customers.handleBan", id),
                        {},
                        {
                            onSuccess: (page) => {
                                ElMessage.success(page.props.flash.success);
                            },
                        }
                    );
                })
                .catch(() => {
                    ElMessage({
                        type: "info",
                        message: "Cancel",
                    });
                });
        };

        const deleteHandler = (id) => {
            ElMessageBox.confirm(
                "Are you sure you want to delete?",
                "Warning",
                {
                    confirmButtonText: "Confirm",
                    cancelButtonText: "Cancel",
                    type: "warning",
                    draggable: true,
                    closeOnClickModal: false,
                }
            )
                .then(() => {
                    router.delete(route("admin.customers.destroy", id), {
                        onSuccess: (page) => {
                            ElMessage.success(page.props.flash.success);
                        },
                    });
                })
                .catch(() => {
                    ElMessage({
                        type: "info",
                        message: "Cancel",
                    });
                });
        };

        const closeDialog = () => {
            state.showDialog = false;
        };

        return {
            ...toRefs(state),
            addNew,
            handleEdit,
            deleteHandler,
            getData,
            onSizeChange,
            onCurrentChange,
            closeDialog,
            handleBan,
        };
    },
    components: { Head, AuthenticatedLayout, Plus, Edit, Delete, Dialog },
};
</script>
