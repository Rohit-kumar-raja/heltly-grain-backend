<script setup>
import Tag from 'primevue/tag';
import AppFormDialog from '@/Components/AppFormDialog.vue';

const props = defineProps({
    visible: { type: Boolean, default: false },
    order: { type: Object, default: null },
});

const emit = defineEmits(['update:visible']);

const getSeverity = (status) => {
    switch (status) {
        case 'delivered': return 'success';
        case 'shipped': return 'info';
        case 'processing': return 'warn';
        case 'cancelled': return 'danger';
        default: return 'secondary';
    }
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR' }).format(value);
};
</script>

<template>
    <AppFormDialog :visible="visible" @update:visible="$emit('update:visible', $event)" title="Order Details"
        :subtitle="order ? 'Order ' + order.order_number : ''" icon="pi-shopping-bag" width="600px" :showFooter="false">
        <div v-if="order" class="space-y-4">
            <!-- Order Info -->
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-slate-50 dark:bg-slate-800 rounded-xl p-4">
                    <span class="block text-sm text-slate-500 dark:text-slate-400 mb-1">Order Number</span>
                    <span class="font-bold text-slate-900 dark:text-white">{{ order.order_number }}</span>
                </div>
                <div class="bg-slate-50 dark:bg-slate-800 rounded-xl p-4">
                    <span class="block text-sm text-slate-500 dark:text-slate-400 mb-1">Status</span>
                    <Tag :value="order.status" :severity="getSeverity(order.status)" />
                </div>
            </div>

            <!-- Customer Info -->
            <div class="bg-slate-50 dark:bg-slate-800 rounded-xl p-4">
                <span class="block text-sm text-slate-500 dark:text-slate-400 mb-2">
                    <i class="pi pi-user mr-2"></i>Customer
                </span>
                <span class="font-semibold text-slate-900 dark:text-white">{{ order.user?.name }}</span>
            </div>

            <!-- Order Items -->
            <div>
                <span class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-3">
                    <i class="pi pi-shopping-cart mr-2"></i>Order Items
                </span>
                <div class="space-y-2">
                    <div v-for="item in order.items" :key="item.id"
                        class="flex justify-between items-center bg-slate-50 dark:bg-slate-800 p-3 rounded-xl">
                        <div>
                            <span class="font-medium text-slate-900 dark:text-white">{{ item.product_name }}</span>
                            <span class="text-sm text-slate-500 dark:text-slate-400 ml-2">x{{ item.quantity }}</span>
                        </div>
                        <span class="font-semibold text-primary-600 dark:text-primary-400">
                            {{ formatCurrency(item.price * item.quantity) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Total -->
            <div class="flex justify-between items-center pt-4 border-t border-slate-200 dark:border-slate-700">
                <span class="text-lg font-bold text-slate-900 dark:text-white">Total</span>
                <span class="text-xl font-bold text-primary-600 dark:text-primary-400">
                    {{ formatCurrency(order.total) }}
                </span>
            </div>

            <!-- Shipping Address -->
            <div v-if="order.address" class="bg-slate-50 dark:bg-slate-800 rounded-xl p-4">
                <span class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                    <i class="pi pi-map-marker mr-2"></i>Shipping Address
                </span>
                <div class="text-sm text-slate-600 dark:text-slate-400 space-y-1">
                    <p class="font-medium text-slate-900 dark:text-white">{{ order.address?.name }}</p>
                    <p>{{ order.address?.address }}</p>
                    <p>{{ order.address?.city }}, {{ order.address?.state }} {{ order.address?.pincode }}</p>
                    <p class="flex items-center gap-2">
                        <i class="pi pi-phone text-xs"></i>{{ order.address?.phone }}
                    </p>
                </div>
            </div>
        </div>
    </AppFormDialog>
</template>
