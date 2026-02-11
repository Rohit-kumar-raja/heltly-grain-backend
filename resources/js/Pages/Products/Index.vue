<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppDataTable from '@/Components/AppDataTable.vue';
import AppConfirmDialog from '@/Components/AppConfirmDialog.vue';
import ProductForm from './ProductForm.vue';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Toast from 'primevue/toast';
import { useToast } from 'primevue/usetoast';

defineOptions({ layout: AdminLayout });

const props = defineProps({
    categories: { type: Array, default: () => [] },
});

const toast = useToast();
const dataTable = ref(null);
const productDialog = ref(false);
const deleteProductDialog = ref(false);
const selectedProduct = ref(null);

const columns = [
    { field: 'image', header: 'Image', sortable: false, searchable: false },
    { field: 'name', header: 'Name', sortable: true, style: 'min-width:12rem' },
    { field: 'description', header: 'Description', sortable: true },
    { field: 'price', header: 'Price', sortable: true },
    { field: 'pack', header: 'Pack Size', sortable: true }
];

const openNew = () => {
    selectedProduct.value = null;
    productDialog.value = true;
};

const editProduct = (product) => {
    selectedProduct.value = { ...product };
    productDialog.value = true;
};

const onProductSaved = () => {
    dataTable.value?.refresh();
};

const confirmDeleteProduct = (product) => {
    selectedProduct.value = product;
    deleteProductDialog.value = true;
};

const deleteProduct = () => {
    router.delete(route('products.destroy', selectedProduct.value.id), {
        onSuccess: () => {
            deleteProductDialog.value = false;
            toast.add({ severity: 'success', summary: 'Successful', detail: 'Product Deleted', life: 3000 });
            dataTable.value?.refresh();
        }
    });
};
</script>

<template>
    <div>
        <Toast />

        <AppDataTable ref="dataTable" title="Manage Products" :apiUrl="route('products.data')" :columns="columns"
            @edit="editProduct" @delete="confirmDeleteProduct">
            <template #header-actions>
                <Button label="New Product" icon="pi pi-plus" @click="openNew"
                    class="bg-primary-500! hover:bg-primary-600! border-primary-500! rounded-xl! shadow-lg! hover:shadow-xl! transition-all! duration-300!" />
            </template>

            <template #cell-image="{ data }">
                <div class="relative group">
                    <img v-if="data.image" :src="data.image" :alt="data.name"
                        class="w-14 h-14 object-cover rounded-xl shadow-md ring-2 ring-white dark:ring-slate-700 group-hover:scale-105 transition-transform duration-200" />
                    <div v-else
                        class="w-14 h-14 bg-linear-to-br from-slate-100 to-slate-200 dark:from-slate-700 dark:to-slate-800 rounded-xl flex items-center justify-center shadow-inner">
                        <i class="pi pi-image text-xl text-slate-400 dark:text-slate-500"></i>
                    </div>
                </div>
            </template>

            <template #cell-price="{ data }">
                <span
                    class="inline-flex items-center px-3 py-1.5 rounded-lg bg-primary-50 dark:bg-primary-500/10 text-primary-700 dark:text-primary-400 font-semibold text-sm">
                    ₹{{ data.price }}
                </span>
            </template>
        </AppDataTable>

        <ProductForm v-model:visible="productDialog" :product="selectedProduct" :categories="categories"
            @saved="onProductSaved" />

        <AppConfirmDialog v-model:visible="deleteProductDialog" title="Delete Product?"
            message="This action cannot be undone." :itemName="selectedProduct?.name" type="danger"
            confirmLabel="Delete" @confirm="deleteProduct" />
    </div>
</template>