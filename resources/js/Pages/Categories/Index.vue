<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppDataTable from '@/Components/AppDataTable.vue';
import { ref } from 'vue';
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import { useForm } from '@inertiajs/vue3';
import Toast from 'primevue/toast';
import { useToast } from 'primevue/usetoast';

defineOptions({ layout: AdminLayout });

const toast = useToast();
const dataTable = ref(null);
const categoryDialog = ref(false);
const isEdit = ref(false);
const submitted = ref(false);

const columns = [
    { field: 'name', header: 'Name', sortable: true },
    { field: 'image', header: 'Image URL', sortable: false },
    { field: 'created_at', header: 'Created At', sortable: true },
];

const form = useForm({
    id: null,
    name: '',
    image: '',
});

const openNew = () => {
    form.reset();
    isEdit.value = false;
    submitted.value = false;
    categoryDialog.value = true;
};

const editCategory = (category) => {
    form.id = category.id;
    form.name = category.name;
    form.image = category.image;
    isEdit.value = true;
    categoryDialog.value = true;
};

const saveCategory = () => {
    submitted.value = true;
    if (!form.name) return;

    if (isEdit.value) {
        form.put(route('categories.update', form.id), {
            onSuccess: () => {
                categoryDialog.value = false;
                toast.add({ severity: 'success', summary: 'Successful', detail: 'Category Updated', life: 3000 });
                dataTable.value?.refresh();
            },
        });
    } else {
        form.post(route('categories.store'), {
            onSuccess: () => {
                categoryDialog.value = false;
                toast.add({ severity: 'success', summary: 'Successful', detail: 'Category Created', life: 3000 });
                dataTable.value?.refresh();
            },
        });
    }
};

const deleteCategory = (category) => {
    if (!confirm('Are you sure you want to delete this category?')) return;

    // We can use Inertia router here directly or emitted event from table if configured, 
    // but AppDataTable emits 'delete', passing the row data.
    // However AppDataTable handles delete internally via router.delete if we don't prevent default? 
    // Actually AppDataTable emits @delete and expects us to handle it or it does nothing?
    // Let's check AppDataTable implementation. It emits 'delete'.

    // Implementation for explicit delete handler:
    form.delete(route('categories.destroy', category.id), {
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Successful', detail: 'Category Deleted', life: 3000 });
            dataTable.value?.refresh();
        }
    });
};

</script>

<template>
    <div>
        <Toast />
        <AppDataTable ref="dataTable" title="Manage Categories" :apiUrl="route('categories.data')" :columns="columns"
            @edit="editCategory" @delete="deleteCategory">
            <template #header-actions>
                <Button label="New Category" icon="pi pi-plus" @click="openNew" />
            </template>
        </AppDataTable>

        <Dialog v-model:visible="categoryDialog" :header="isEdit ? 'Edit Category' : 'New Category'" :modal="true"
            class="p-fluid w-96">
            <div class="field mb-4">
                <label for="name" class="block text-900 font-medium mb-2">Name</label>
                <InputText id="name" v-model.trim="form.name" required autofocus
                    :class="{ 'p-invalid': submitted && !form.name }" />
                <small class="p-error" v-if="submitted && !form.name">Name is required.</small>
            </div>
            <div class="field mb-4">
                <label for="image" class="block text-900 font-medium mb-2">Image URL (Optional)</label>
                <InputText id="image" v-model.trim="form.image" />
            </div>

            <template #footer>
                <Button label="Cancel" icon="pi pi-times" text @click="categoryDialog = false" />
                <Button label="Save" icon="pi pi-check" text @click="saveCategory" />
            </template>
        </Dialog>
    </div>
</template>
