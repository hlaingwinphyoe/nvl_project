<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-4 gap-4 mb-6">
                    <Link
                        :href="
                            route('admin.orders.index', {
                                start_date: start,
                                end_date: end,
                            })
                        "
                        class="bg-white p-6 shadow rounded flex items-center justify-between"
                    >
                        <div>
                            <h6 class="text-gray-500 text-sm mb-3.5">
                                Today Orders
                            </h6>
                            <h1 class="font-bold text-2xl">
                                {{ todayOrderCount }}
                            </h1>
                        </div>
                        <div class="text-green-500">
                            <img src="/icon1.svg" alt="" class="h-10" />
                        </div>
                    </Link>
                    <Link
                        :href="route('admin.customers.index')"
                        class="bg-white p-6 shadow rounded flex items-center justify-between"
                    >
                        <div>
                            <h6 class="text-gray-500 text-sm mb-3.5">
                                Total Customers
                            </h6>
                            <h1 class="font-bold text-2xl">
                                {{ totalCustomers }}
                            </h1>
                        </div>
                        <div class="text-primary-500">
                            <img src="/icon3.svg" alt="" class="h-10" />
                        </div>
                    </Link>
                    <Link
                        :href="route('admin.series.index')"
                        class="bg-white p-6 shadow rounded flex items-center justify-between"
                    >
                        <div>
                            <h6 class="text-gray-500 text-sm mb-3.5">
                                Total Series
                            </h6>
                            <h1 class="font-bold text-2xl">
                                {{ totalSeries }}
                            </h1>
                        </div>
                        <div class="text-yellow-500">
                            <img src="/icon4.svg" alt="" class="h-10" />
                        </div>
                    </Link>
                    <div
                        class="bg-white p-6 shadow rounded flex items-center justify-between"
                    >
                        <div>
                            <h6 class="text-gray-500 text-sm mb-3.5">
                                New Customers
                            </h6>
                            <h1 class="font-bold text-2xl">
                                {{ newCustomers }}
                            </h1>
                        </div>
                        <div class="text-gray-800">
                            <img src="/icon2.svg" alt="" class="h-10" />
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div
                        class="bg-white overflow-hidden shadow sm:rounded-lg p-6 h-fit"
                    >
                        <h5 class="font-bold text-lg mb-3 text-primary-500">
                            Last 7 days Orders
                        </h5>
                        <div class="w-full h-auto">
                            <Line
                                :data="testsData"
                                :options="TestsOptions"
                                v-if="
                                    showLineChart && testsData.labels.length > 0
                                "
                            />
                            <h4
                                v-else
                                class="text-red-600 text-lg font-semibold"
                            >
                                There is no data for this week.
                            </h4>
                        </div>
                    </div>
                    <div
                        class="bg-white overflow-hidden shadow sm:rounded-lg h-fit"
                    >
                        <div class="py-6 px-2 text-gray-900">
                            <div class="relative overflow-hideen">
                                <h4
                                    class="font-bold text-lg ml-3 mb-3 text-primary-500"
                                >
                                    More Than 10 Orders
                                </h4>
                                <el-table
                                    :data="moreThanTenOrders"
                                    style="width: 100%"
                                >
                                    <el-table-column
                                        prop="customer"
                                        label="Name"
                                    />
                                    <el-table-column
                                        prop="order_count"
                                        label="Orders"
                                    />
                                    <el-table-column
                                        prop="user"
                                        label="Created By"
                                    />
                                    <el-table-column prop="time" label="Date" />
                                    <el-table-column
                                        label="Action"
                                        align="center"
                                    >
                                        <template #default="scope">
                                            <el-tooltip
                                                class="box-item"
                                                content="Make VIP"
                                                placement="top"
                                            >
                                                <el-button
                                                    type="success"
                                                    circle
                                                    @click="
                                                        handleEdit(scope.row)
                                                    "
                                                >
                                                    <el-icon><Check /></el-icon>
                                                </el-button>
                                            </el-tooltip>
                                        </template>
                                    </el-table-column>
                                </el-table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import { Line } from "vue-chartjs";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";

import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    LineElement,
    CategoryScale,
    LinearScale,
    PointElement,
} from "chart.js";
import { onMounted, reactive, toRefs } from "vue";
import { Check } from "@element-plus/icons-vue";

ChartJS.register(
    Title,
    Tooltip,
    Legend,
    LineElement,
    CategoryScale,
    LinearScale,
    PointElement
);

export default {
    props: [
        "moreThanTenOrders",
        "todayOrderCount",
        "totalCustomers",
        "totalSeries",
        "newCustomers",
        "labels",
        "datasets",
        "start_date",
        "end_date",
        "today",
    ],
    components: {
        AuthenticatedLayout,
        Head,
        Line,
        Link,
        Check,
    },
    setup(props) {
        const state = reactive({
            showLineChart: false,
            start: "",
            end: "",
            testsData: {
                labels: [],
                datasets: [
                    {
                        label: "Total Orders",
                        data: [],
                        fill: false,
                        borderColor: "#14b8a6",
                        borderWidth: 1.5,
                        tension: 0.1,
                    },
                ],
            },
        });

        const TestsOptions = {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,

                    labels: {
                        font: {
                            size: 12,
                            weight: "600",
                        },
                    },
                },
            },
        };

        onMounted(() => {
            state.testsData.labels = props.labels;
            state.testsData.datasets[0].data = props.datasets;

            state.showLineChart = true;

            if (props.today) {
                state.start = props.today + "T00:00:00";
                state.end = props.today + "T23:59:59";
            }
        });

        return {
            ...toRefs(state),
            TestsOptions,
        };
    },
};
</script>
