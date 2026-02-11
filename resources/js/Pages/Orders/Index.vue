<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppDataTable from '@/Components/AppDataTable.vue';
import OrderDetails from './OrderDetails.vue';
import OrderStatusForm from './OrderStatusForm.vue';
import { ref } from 'vue';
import Tag from 'primevue/tag';
import Toast from 'primevue/toast';

defineOptions({ layout: AdminLayout });

const dataTable = ref(null);
const statusDialog = ref(false);
const orderDialog = ref(false);
const selectedOrder = ref(null);

const columns = [
    { field: 'order_number', header: 'Order No.', sortable: true, style: 'min-width:10rem' },
    { field: 'user.name', header: 'Customer', sortable: true, style: 'min-width:10rem' },
    { field: 'created_at', header: 'Date', sortable: true },
    { field: 'total', header: 'Total', sortable: true },
    { field: 'status', header: 'Status', sortable: true }
];

const getSeverity = (status) => {
    switch (status) {
        case 'delivered': return 'success';
        case 'shipped': return 'info';
        case 'processing': return 'warn';
        case 'cancelled': return 'danger';
        default: return 'secondary';
    }
};

const viewOrder = (order) => {
    selectedOrder.value = order;
    orderDialog.value = true;
};

const openStatusDialog = (order) => {
    selectedOrder.value = order;
    statusDialog.value = true;
};

const onStatusSaved = () => {
    dataTable.value?.refresh();
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR' }).format(value);
};
</script>

<template>
    <div>
        <Toast />

        <AppDataTable ref="dataTable" title="Manage Orders" :apiUrl="route('orders.data')" :columns="columns"
            :showDelete="false" :showView="true" @view="viewOrder" @edit="openStatusDialog">
            <template #cell-created_at="{ data }">
                {{ new Date(data.created_at).toLocaleDateString() }}
            </template>

            <template #cell-total="{ data }">
                <span
                    class="inline-flex items-center px-3 py-1.5 rounded-lg bg-primary-50 dark:bg-primary-500/10 text-primary-700 dark:text-primary-400 font-semibold text-sm">
                    {{ formatCurrency(data.total) }}
                </span>
            </template>

            <template #cell-status="{ data }">
                <Tag :value="data.status" :severity="getSeverity(data.status)" />
            </template>
        </AppDataTable>

        <!-- Order Details (handles its own dialog) -->
        <OrderDetails v-model:visible="orderDialog" :order="selectedOrder" />

        <!-- Order Status Form (handles its own dialog) -->
        <OrderStatusForm v-model:visible="statusDialog" :order="selectedOrder" @saved="onStatusSaved" />
    </div>
</template>
