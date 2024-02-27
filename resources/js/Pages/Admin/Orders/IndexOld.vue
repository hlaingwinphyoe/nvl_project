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
                                    placeholder="Search orders..."
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
                            <el-table :data="orders.data" table-layout="fixed">
                                <el-table-column
                                    type="index"
                                    label="Sr."
                                    width="100"
                                />
                                <el-table-column
                                    prop="customer_name"
                                    label="Customer"
                                    sortable
                                    align="center"
                                />
                                <el-table-column
                                    prop="total"
                                    label="Total Price"
                                    align="center"
                                />
                                <el-table-column
                                    label="No. of Orders"
                                    align="center"
                                >
                                    <template #default="scope">
                                        <span
                                            class="px-2 text-xs py-0.5 rounded border border-green-500 bg-green-50"
                                        >
                                            {{ scope.row.number_of_orders }}
                                        </span>
                                    </template>
                                </el-table-column>
                                <el-table-column
                                    prop="user"
                                    label="Created By"
                                    align="center"
                                />
                                <el-table-column
                                    prop="created_at"
                                    label="Joined At"
                                    sortable
                                    align="center"
                                />

                                <el-table-column
                                    label="Actions"
                                    align="center"
                                    width="150px"
                                >
                                    <template #default="scope">
                                        <el-tooltip
                                            class="box-item"
                                            content="Detail"
                                            placement="top"
                                        >
                                            <el-button
                                                type="success"
                                                style="margin-bottom: 5px"
                                                circle
                                                @click="handleDetail(scope.row)"
                                            >
                                                <el-icon><View /></el-icon>
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
                                    :total="orders.total"
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
            :customers="customers"
            :series="series"
        />

        <Show
            :show="showDetail"
            @closed="closeDialog"
            :title="dialog.dialogTitle"
            :data="dialog.dialogData"
        />
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Delete, View, Plus } from "@element-plus/icons-vue";
import { Head, router } from "@inertiajs/vue3";
import { reactive, toRefs, watch } from "vue";
import { ElMessage, ElMessageBox } from "element-plus";
import debounce from "lodash.debounce";
import Dialog from "./Dialog.vue";
import Show from "./Detail.vue";
export default {
    props: ["orders", "customers", "series"],
    setup(props) {
        const state = reactive({
            showDialog: false,
            showDetail: false,
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

        const handleDetail = (row) => {
            state.dialog.dialogTitle = "Detail";
            state.dialog.dialogData = JSON.parse(JSON.stringify(row));
            state.showDetail = true;
        };

        watch(
            () => state.param.search,
            debounce(() => {
                getData();
            }, 500)
        );

        const changeStatus = (row) => {
            ElMessageBox.confirm(
                "Are you sure want to change status of this movie?",
                "Confirmation",
                {
                    confirmButtonText: "Confirm",
                    cancelButtonText: "Cancel",
                    type: "warning",
                    draggable: true,
                    closeOnClickModal: false,
                }
            )
                .then(() => {
                    router.patch(
                        route("admin.orders.change-status", row.id),
                        {},
                        {
                            preserveState: true,
                            onSuccess: (page) => {
                                ElMessage.success(page.props.flash.success);
                            },
                            onError: (page) => {
                                ElMessage.error(page.props.flash.error);
                            },
                        }
                    );
                })
                .catch(() => {
                    router.reload();
                    ElMessage({
                        type: "info",
                        message: "Cancel",
                    });
                });
        };

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
            router.get(route("admin.orders.index"), state.param, {
                preserveScroll: true,
                preserveState: true,
                replace: true,

                onFinish: (page) => {
                    state.isLoading = false;
                },
            });
        }

        const closeDialog = () => {
            state.showDialog = false;
            state.showDetail = false;
        };

        return {
            ...toRefs(state),
            addNew,
            handleDetail,
            getData,
            onSizeChange,
            onCurrentChange,
            closeDialog,
            changeStatus,
        };
    },
    components: {
        Head,
        AuthenticatedLayout,
        Plus,
        Delete,
        Dialog,
        View,
        Show,
    },
};
</script>
