<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppDataTable from '@/Components/AppDataTable.vue';
import { router } from '@inertiajs/vue3';
defineOptions({ layout: AdminLayout });

const columns = [
    { field: 'name', header: 'Name', sortable: true },
    { field: 'email', header: 'Email', sortable: true },
    { field: 'created_at', header: 'Joined Date', sortable: true },
];

const viewUser = (data: any) => {
    router.visit(route('app-users.show', data.id));
};




</script>

<template>
    <div>
        <AppDataTable title="App Users" :apiUrl="route('app-users.data')" :columns="columns" :showDelete="false"
            :showView="true" @view="viewUser($event)" :showEdit="false">
            <template #cell-created_at="{ data }">
                {{ new Date(data.created_at).toLocaleDateString() }}
            </template>
        </AppDataTable>
    </div>
</template>
