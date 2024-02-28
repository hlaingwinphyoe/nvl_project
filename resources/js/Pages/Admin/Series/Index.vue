<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-4">
                                <el-button
                                    type="primary"
                                    size="large"
                                    @click="addNew"
                                >
                                    <el-icon class="mr-1">
                                        <Plus />
                                    </el-icon>
                                    Add New
                                </el-button>
                                <el-date-picker
                                    v-model="filter.dateValue"
                                    type="daterange"
                                    value-format="YYYY-MM-DD"
                                    unlink-panels
                                    range-separator="to"
                                    start-placeholder="Start Date"
                                    end-placeholder="End Date"
                                    size="large"
                                />
                                <div class="flex">
                                    <el-button
                                        type="warning"
                                        size="large"
                                        @click="search(1)"
                                    >
                                        <el-icon class="mr-1">
                                            <Search />
                                        </el-icon>
                                        Search
                                    </el-button>
                                    <el-button
                                        type="danger"
                                        @click="reset"
                                        size="large"
                                    >
                                        <el-icon>
                                            <Refresh />
                                        </el-icon>
                                    </el-button>
                                </div>
                            </div>
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
                                :data="series.data"
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
                                    width="100"
                                />
                                <el-table-column
                                    label="Title"
                                    sortable
                                    align="center"
                                >
                                    <template #default="scope">
                                        {{ scope.row.title }}
                                    </template>
                                </el-table-column>
                                <el-table-column label="Price" align="center">
                                    <template #default="scope">
                                        {{ scope.row.price }} Ks
                                    </template>
                                </el-table-column>
                                <el-table-column
                                    prop="user"
                                    label="Created By"
                                    align="center"
                                />
                                <el-table-column
                                    label="Published"
                                    align="center"
                                    width="100px"
                                >
                                    <template #default="scope">
                                        <el-switch
                                            v-model="scope.row.is_published"
                                            :active-value="1"
                                            :inactive-value="0"
                                            active-color="#13ce66"
                                            inactive-color="#ff4949"
                                            @click="changeStatus(scope.row)"
                                        />
                                    </template>
                                </el-table-column>
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
                                    :total="series.total"
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
import { Edit, Plus, Refresh, Search } from "@element-plus/icons-vue";
import { Head, router } from "@inertiajs/vue3";
import { reactive, toRefs, watch } from "vue";
import { ElMessage, ElMessageBox } from "element-plus";
import debounce from "lodash.debounce";
import Dialog from "./Dialog.vue";
export default {
    props: ["series"],
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
            filter: {
                dateValue: [],
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
                        route("admin.series.change-status", row.id),
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
            router.get(route("admin.series.index"), state.param, {
                preserveScroll: true,
                preserveState: true,
                replace: true,

                onFinish: (page) => {
                    state.isLoading = false;
                },
            });
        }

        const search = (page) => {
            if (state.filter.dateValue && state.filter.dateValue.length) {
                state.param.start_date = `${state.filter.dateValue[0]}T00:00:00`;
                state.param.end_date = `${state.filter.dateValue[1]}T23:59:59`;
            }

            state.param.page = page;
            state.param.page_size = 10;
            getData();
        };

        const reset = () => {
            state.param = {};
            search(1);
        };

        const closeDialog = () => {
            state.showDialog = false;
        };

        return {
            ...toRefs(state),
            addNew,
            handleEdit,
            getData,
            onSizeChange,
            onCurrentChange,
            closeDialog,
            changeStatus,
            search,
            reset,
        };
    },
    components: {
        Head,
        AuthenticatedLayout,
        Plus,
        Edit,
        Dialog,
        Search,
        Refresh,
    },
};
</script>
