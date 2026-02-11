<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Chart from 'primevue/chart';
import { ref, onMounted } from 'vue';

defineOptions({ layout: AdminLayout });

const props = defineProps({
    stats: { type: Array, default: () => [] },
    recentOrders: { type: Array, default: () => [] },
    topProducts: { type: Array, default: () => [] },
    salesChart: { type: Object, default: () => ({ labels: [], revenue: [], orders: [] }) },
    categoryChart: { type: Object, default: () => ({ labels: [], data: [] }) },
    weeklyOrders: { type: Object, default: () => ({ labels: [], data: [], total: 0 }) },
    userGrowth: { type: Object, default: () => ({ labels: [], newUsers: [], totalUsers: [] }) },
});

// Chart data refs
const salesChartData = ref();
const salesChartOptions = ref();
const pieData = ref();
const pieOptions = ref();
const barChartData = ref();
const barChartOptions = ref();
const areaChartData = ref();
const areaChartOptions = ref();

const chartColors = ['#4c8526', '#10b981', '#3b82f6', '#f59e0b', '#8b5cf6', '#ec4899', '#f97316', '#06b6d4'];
const chartColorsAlpha = chartColors.map(c => c + 'B3');

onMounted(() => {
    // Sales Line Chart
    salesChartData.value = {
        labels: props.salesChart.labels,
        datasets: [
            {
                label: 'Revenue',
                data: props.salesChart.revenue,
                fill: true,
                backgroundColor: 'rgba(76, 133, 38, 0.1)',
                borderColor: '#4c8526',
                tension: 0.4,
                pointBackgroundColor: '#4c8526',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4
            },
            {
                label: 'Orders',
                data: props.salesChart.orders,
                fill: true,
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderColor: '#3b82f6',
                tension: 0.4,
                pointBackgroundColor: '#3b82f6',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4
            }
        ]
    };

    salesChartOptions.value = {
        maintainAspectRatio: false,
        plugins: {
            legend: {
                labels: { color: '#64748b', usePointStyle: true }
            }
        },
        scales: {
            x: {
                ticks: { color: '#64748b' },
                grid: { color: 'rgba(100, 116, 139, 0.1)' }
            },
            y: {
                ticks: { color: '#64748b' },
                grid: { color: 'rgba(100, 116, 139, 0.1)' }
            }
        }
    };

    // Product Categories Doughnut
    pieData.value = {
        labels: props.categoryChart.labels,
        datasets: [{
            data: props.categoryChart.data,
            backgroundColor: chartColorsAlpha.slice(0, props.categoryChart.labels.length),
            hoverBackgroundColor: chartColors.slice(0, props.categoryChart.labels.length),
            borderWidth: 0
        }]
    };

    pieOptions.value = {
        cutout: '60%',
        plugins: {
            legend: {
                position: 'bottom',
                labels: { color: '#64748b', usePointStyle: true, padding: 20 }
            }
        }
    };

    // Weekly Orders Bar Chart
    barChartData.value = {
        labels: props.weeklyOrders.labels,
        datasets: [{
            label: 'Orders This Week',
            data: props.weeklyOrders.data,
            backgroundColor: 'rgba(76, 133, 38, 0.8)',
            borderRadius: 8,
            borderSkipped: false,
        }]
    };

    barChartOptions.value = {
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            x: {
                ticks: { color: '#64748b' },
                grid: { display: false }
            },
            y: {
                ticks: { color: '#64748b' },
                grid: { color: 'rgba(100, 116, 139, 0.1)' }
            }
        }
    };

    // Area Chart - User Growth
    areaChartData.value = {
        labels: props.userGrowth.labels,
        datasets: [{
            label: 'New Users',
            data: props.userGrowth.newUsers,
            fill: true,
            backgroundColor: 'rgba(16, 185, 129, 0.2)',
            borderColor: '#10b981',
            tension: 0.4,
        },
        {
            label: 'Total Users',
            data: props.userGrowth.totalUsers,
            fill: true,
            backgroundColor: 'rgba(59, 130, 246, 0.2)',
            borderColor: '#3b82f6',
            tension: 0.4,
        }]
    };

    areaChartOptions.value = {
        maintainAspectRatio: false,
        plugins: {
            legend: {
                labels: { color: '#64748b', usePointStyle: true }
            }
        },
        scales: {
            x: {
                ticks: { color: '#64748b' },
                grid: { color: 'rgba(100, 116, 139, 0.1)' }
            },
            y: {
                ticks: { color: '#64748b' },
                grid: { color: 'rgba(100, 116, 139, 0.1)' }
            }
        }
    };
});

const getColorClass = (color) => {
    const colors = {
        primary: 'bg-primary-100 dark:bg-primary-900/30 text-primary-600',
        emerald: 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600',
        blue: 'bg-blue-100 dark:bg-blue-900/30 text-blue-600',
        amber: 'bg-amber-100 dark:bg-amber-900/30 text-amber-600',
    };
    return colors[color] || colors.primary;
};

const getBorderClass = (color) => {
    const colors = {
        primary: 'border-primary-500',
        emerald: 'border-emerald-500',
        blue: 'border-blue-500',
        amber: 'border-amber-500',
    };
    return colors[color] || colors.primary;
};
</script>

<template>
    <div class="space-y-6 h-[calc(100vh-140px)]">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div v-for="stat in stats" :key="stat.label"
                class="bg-white dark:bg-slate-800 rounded-2xl p-5 shadow-sm border-l-4 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg"
                :class="getBorderClass(stat.color)">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500 dark:text-slate-400 font-medium">{{ stat.label }}</p>
                        <p class="text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ stat.value }}</p>
                        <p class="text-xs mt-2 font-medium"
                            :class="stat.positive ? 'text-emerald-500' : 'text-red-500'">
                            <i :class="stat.positive ? 'pi pi-arrow-up' : 'pi pi-arrow-down'" class="mr-1"></i>
                            {{ stat.change }} from last month
                        </p>
                    </div>
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center"
                        :class="getColorClass(stat.color)">
                        <i :class="['pi', stat.icon, 'text-2xl']"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row 1 -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Sales Overview - Large -->
            <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Sales Overview</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Revenue & orders trend</p>
                    </div>
                </div>
                <Chart type="line" :data="salesChartData" :options="salesChartOptions" class="h-72" />
            </div>

            <!-- Product Categories -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm">
                <div class="mb-4">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Product Categories</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Products per category</p>
                </div>
                <Chart type="doughnut" :data="pieData" :options="pieOptions" class="h-64" />
            </div>
        </div>

        <!-- Charts Row 2 -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Weekly Orders Bar Chart -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Weekly Orders</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Orders this week</p>
                    </div>
                    <span class="text-2xl font-bold text-primary-600">{{ weeklyOrders.total }}</span>
                </div>
                <Chart type="bar" :data="barChartData" :options="barChartOptions" class="h-64" />
            </div>

            <!-- User Growth Area Chart -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">User Growth</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400">New & total users</p>
                    </div>
                </div>
                <Chart type="line" :data="areaChartData" :options="areaChartOptions" class="h-64" />
            </div>
        </div>

        <!-- Row 3: Top Products + Recent Orders -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Top Products List -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm">
                <div class="mb-4">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Top Products</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Best sellers</p>
                </div>
                <div class="space-y-4">
                    <div v-for="(product, index) in topProducts" :key="product.name"
                        class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
                        <div class="flex items-center gap-3">
                            <span
                                class="w-8 h-8 bg-primary-100 dark:bg-primary-900/30 text-primary-600 rounded-lg flex items-center justify-center font-bold text-sm">
                                {{ index + 1 }}
                            </span>
                            <div>
                                <p class="font-medium text-slate-900 dark:text-white text-sm">{{ product.name }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">{{ product.sales }} sales</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-slate-900 dark:text-white text-sm">{{ product.revenue }}</p>
                        </div>
                    </div>
                    <div v-if="!topProducts.length" class="text-center py-8 text-slate-400 dark:text-slate-500">
                        <i class="pi pi-box text-3xl mb-2"></i>
                        <p class="text-sm">No product data yet</p>
                    </div>
                </div>
            </div>

            <!-- Recent Orders Table -->
            <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Recent Orders</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Latest transactions</p>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-slate-200 dark:border-slate-700">
                                <th
                                    class="text-left py-3 px-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                    Order ID</th>
                                <th
                                    class="text-left py-3 px-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                    Customer</th>
                                <th
                                    class="text-left py-3 px-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                    Product</th>
                                <th
                                    class="text-left py-3 px-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                    Amount</th>
                                <th
                                    class="text-left py-3 px-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                    Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="order in recentOrders" :key="order.id"
                                class="border-b border-slate-100 dark:border-slate-700/50 hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                                <td class="py-4 px-4 font-medium text-slate-900 dark:text-white">{{ order.id }}</td>
                                <td class="py-4 px-4 text-slate-600 dark:text-slate-300">{{ order.customer }}</td>
                                <td class="py-4 px-4 text-slate-600 dark:text-slate-300">{{ order.product }}</td>
                                <td class="py-4 px-4 font-semibold text-slate-900 dark:text-white">{{ order.amount }}
                                </td>
                                <td class="py-4 px-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-medium" :class="{
                                        'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400': order.statusColor === 'emerald',
                                        'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400': order.statusColor === 'blue',
                                        'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400': order.statusColor === 'amber',
                                        'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400': order.statusColor === 'purple',
                                        'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400': order.statusColor === 'red',
                                    }">
                                        {{ order.status }}
                                    </span>
                                </td>
                            </tr>
                            <tr v-if="!recentOrders.length">
                                <td colspan="5" class="text-center py-8 text-slate-400 dark:text-slate-500">
                                    <i class="pi pi-inbox text-3xl mb-2 block"></i>
                                    No orders yet
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>
