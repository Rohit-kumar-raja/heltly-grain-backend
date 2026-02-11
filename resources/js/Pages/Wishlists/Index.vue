<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppDataTable from '@/Components/AppDataTable.vue';
import { ref } from 'vue';
import Tag from 'primevue/tag';
import Toast from 'primevue/toast';
import ConfirmDialog from 'primevue/confirmdialog';
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';
import { router } from '@inertiajs/vue3';

defineOptions({ layout: AdminLayout });

const confirm = useConfirm();
const toast = useToast();
const dataTable = ref(null);

const columns = [
    { field: 'user_name', header: 'Customer', sortable: true, style: 'min-width:10rem' },
    { field: 'user_email', header: 'Email', sortable: true, style: 'min-width:12rem' },
    { field: 'product_name', header: 'Product', sortable: true, style: 'min-width:12rem' },
    { field: 'product_price', header: 'Price', sortable: true, style: 'min-width:8rem' },
    { field: 'created_at', header: 'Added On', sortable: true, style: 'min-width:10rem' }
];

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR' }).format(value);
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-IN', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const confirmDelete = (item) => {
    confirm.require({
        message: `Are you sure you want to remove "${item.product_name}" from ${item.user_name}'s wishlist?`,
        header: 'Delete Confirmation',
        icon: 'pi pi-exclamation-triangle',
        rejectLabel: 'Cancel',
        acceptLabel: 'Delete',
        rejectProps: {
            severity: 'secondary',
            outlined: true
        },
        acceptProps: {
            severity: 'danger'
        },
        accept: () => {
            router.delete(route('wishlists.destroy', item.id), {
                onSuccess: () => {
                    toast.add({
                        severity: 'success',
                        summary: 'Deleted',
                        detail: 'Wishlist item removed successfully',
                        life: 3000
                    });
                    dataTable.value?.refresh();
                }
            });
        }
    });
};
</script>

<template>
    <div>
        <Toast />
        <ConfirmDialog />

        <AppDataTable ref="dataTable" title="Wishlists" subtitle="View customer wishlist items" icon="pi-heart"
            :apiUrl="route('wishlists.data')" :columns="columns" :showAdd="false" :showEdit="false" :showView="false"
            @delete="confirmDelete">

            <!-- Product with image -->
            <template #cell-product_name="{ data }">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-slate-100 dark:bg-slate-700 overflow-hidden shrink-0">
                        <img v-if="data.product_image" :src="data.product_image" :alt="data.product_name"
                            class="w-full h-full object-cover" />
                        <div v-else class="w-full h-full flex items-center justify-center">
                            <i class="pi pi-image text-slate-400"></i>
                        </div>
                    </div>
                    <span class="font-medium text-slate-900 dark:text-slate-100">{{ data.product_name }}</span>
                </div>
            </template>

            <!-- Customer with avatar -->
            <template #cell-user_name="{ data }">
                <div class="flex items-center gap-3">
                    <div
                        class="w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                        <span class="text-primary-600 dark:text-primary-400 font-semibold text-xs">
                            {{ data.user_name?.charAt(0)?.toUpperCase() || '?' }}
                        </span>
                    </div>
                    <span class="font-medium text-slate-700 dark:text-slate-300">{{ data.user_name }}</span>
                </div>
            </template>

            <!-- Email -->
            <template #cell-user_email="{ data }">
                <span class="text-slate-500 dark:text-slate-400 text-sm">{{ data.user_email }}</span>
            </template>

            <!-- Price -->
            <template #cell-product_price="{ data }">
                <span
                    class="inline-flex items-center px-3 py-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 font-semibold text-sm">
                    {{ formatCurrency(data.product_price) }}
                </span>
            </template>

            <!-- Date -->
            <template #cell-created_at="{ data }">
                <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                    <i class="pi pi-calendar text-xs"></i>
                    {{ formatDate(data.created_at) }}
                </div>
            </template>
        </AppDataTable>
    </div>
</template>
