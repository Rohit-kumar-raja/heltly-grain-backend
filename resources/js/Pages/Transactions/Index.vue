<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppDataTable from '@/Components/AppDataTable.vue';
import { ref } from 'vue';
import Tag from 'primevue/tag';

defineOptions({ layout: AdminLayout });

const columns = [
    { field: 'transaction_id', header: 'Transaction ID', sortable: true },
    { field: 'order_number', header: 'Order No', sortable: true },
    { field: 'user_name', header: 'User', sortable: true },
    { field: 'amount', header: 'Amount', sortable: true },
    { field: 'gateway', header: 'Gateway', sortable: true },
    { field: 'status', header: 'Status', sortable: true },
    { field: 'created_at', header: 'Date', sortable: true },
];

const getSeverity = (status) => {
    switch (status) {
        case 'success': return 'success';
        case 'pending': return 'warn';
        case 'failed': return 'danger';
        case 'refunded': return 'info';
        default: return 'secondary';
    }
};

</script>

<template>
    <div>
        <AppDataTable title="Transactions" :apiUrl="route('transactions.data')" :columns="columns" :showDelete="false"
            :showView="false" :showEdit="false">

            <template #cell-status="{ data }">
                <Tag :value="data.status" :severity="getSeverity(data.status)" />
            </template>

            <template #cell-gateway="{ data }">
                <span class="uppercase text-xs font-bold text-slate-500">{{ data.gateway }}</span>
            </template>

        </AppDataTable>
    </div>
</template>
