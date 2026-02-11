<script setup>
import { ref, onMounted } from 'vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputText from 'primevue/inputtext';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import Button from 'primevue/button';
import axios from 'axios';

const props = defineProps({
    apiUrl: {
        type: String,
        required: true
    },
    columns: {
        type: Array,
        required: true
    },
    title: {
        type: String,
        default: 'Manage Records'
    },
    // Action button visibility
    showActions: {
        type: Boolean,
        default: true
    },
    showEdit: {
        type: Boolean,
        default: true
    },
    showDelete: {
        type: Boolean,
        default: true
    },
    showView: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['edit', 'delete', 'view']);

const data = ref([]);
const totalRecords = ref(0);
const loading = ref(false);
const filters = ref({
    'global': { value: null, matchMode: 'contains' }
});

let debounceTimer = null;
const onSearch = () => {
    if (debounceTimer) clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        lazyParams.value.filters = filters.value;
        loadLazyData(lazyParams.value);
    }, 500);
};

const lazyParams = ref({
    first: 0,
    rows: 10,
    sortField: null,
    sortOrder: null,
    filters: {}
});

const loadLazyData = (event) => {
    loading.value = true;
    lazyParams.value = { ...lazyParams.value, ...event };

    // Find sort column index if sortField is present
    let orderRaw = [];
    if (event.sortField) {
        const colIndex = props.columns.findIndex(c => (c.name || c.field) === event.sortField);
        if (colIndex !== -1) {
            orderRaw.push({ column: colIndex, dir: event.sortOrder === 1 ? 'asc' : 'desc' });
        }
    }

    const params = {
        draw: Number(event.first / event.rows) + 1,
        start: event.first,
        length: event.rows,
        search: { value: filters.value.global?.value || '', regex: false },
        columns: props.columns.map(c => ({
            data: c.field,
            name: c.name || c.field,
            searchable: c.searchable ?? true,
            orderable: c.sortable ?? true,
            search: { value: '', regex: false }
        })),
        order: orderRaw
    };

    axios.get(props.apiUrl, { params }).then((response) => {
        data.value = response.data.data;
        totalRecords.value = response.data.recordsTotal;
        loading.value = false;
    });
};

const refresh = () => {
    loadLazyData(lazyParams.value);
};

// Expose refresh method to parent
defineExpose({ refresh });

onMounted(() => {
    loadLazyData(lazyParams.value);
});
</script>

<template>
    <div
        class="h-[calc(100vh-112px)] flex flex-col bg-white dark:bg-slate-900 rounded-2xl shadow-lg border border-slate-200 dark:border-slate-700 overflow-hidden">
        <!-- Header Section -->
        <div
            class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5 p-6 bg-linear-to-r from-primary-50/50 to-emerald-50/50 dark:from-primary-900/20 dark:to-emerald-900/20 border-b border-slate-200 dark:border-slate-700">
            <div class="flex items-center gap-4">
                <div
                    class="w-12 h-12 rounded-xl bg-linear-to-r from-primary-500 to-primary-700 flex items-center justify-center text-white text-xl shadow-lg shadow-primary-500/30">
                    <i class="pi pi-database"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 dark:text-slate-100 tracking-tight">{{ title }}</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">{{ totalRecords }} total records</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-3 w-full sm:w-auto">
                <IconField class="flex-1 sm:flex-none sm:w-72 ">
                    <InputIcon class="pi pi-search" />
                    <InputText v-model="filters.global.value" placeholder="Search records..." @input="onSearch"
                        class="w-full rounded-xl!" />
                </IconField>
                <slot name="header-actions" />
            </div>
        </div>

        <DataTable :value="data" lazy paginator :first="lazyParams.first" :rows="lazyParams.rows"
            :totalRecords="totalRecords" :loading="loading" @page="loadLazyData" @sort="loadLazyData"
            @filter="loadLazyData" v-model:filters="filters" :showGridlines="false" tableStyle="min-width: 50rem"
            paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
            :rowsPerPageOptions="[5, 10, 25]" scrollable scrollHeight="flex"
            currentPageReportTemplate="Showing {first} to {last} of {totalRecords} records"
            class="flex-1 datatable-fullscreen" :rowHover="true">
            <template #empty>
                <div class="flex flex-col items-center justify-center py-16 px-8 text-center h-[calc(100vh-365px)]">
                    <i class="pi pi-inbox text-6xl text-slate-300 dark:text-slate-600 mb-4"></i>
                    <p class="text-lg font-semibold text-slate-500 dark:text-slate-400">No records found</p>
                    <p class="text-sm text-slate-400 dark:text-slate-500 mt-2">Try adjusting your search or filters
                    </p>
                </div>
            </template>
            <template #loading>
                <div class="flex flex-col items-center justify-center py-16 px-8 text-center">
                    <i class="pi pi-spin pi-spinner text-6xl text-primary-500 mb-4"></i>
                    <p class="text-lg font-semibold text-slate-500 dark:text-slate-400">Loading data...</p>
                </div>
            </template>

            <!-- Default S.NO Column -->
            <Column header="S.NO" :style="{ width: '0.5rem' }">
                <template #body="slotProps">
                    {{ lazyParams.first + slotProps.index + 1 }}
                </template>
            </Column>
            <Column v-for="col in columns" :key="col.field" :field="col.field" :header="col.header"
                :sortable="col.sortable" :style="col.style">
                <template #body="slotProps">
                    <slot :name="`cell-${col.field}`" :data="slotProps.data">
                        {{ slotProps.data[col.field] }}
                    </slot>
                </template>
            </Column>

            <!-- Built-in Actions Column -->
            <Column v-if="showActions" header="Actions" :style="{ minWidth: '8rem' }">
                <template #body="slotProps">
                    <div class="flex items-center gap-2">
                        <Button v-if="showView" icon="pi pi-eye" rounded
                            class="w-9! h-9! bg-slate-50! dark:bg-slate-500/10! text-slate-600! dark:text-slate-400! border-slate-200! dark:border-slate-500/30! hover:bg-slate-100! dark:hover:bg-slate-500/20! transition-colors"
                            @click="emit('view', slotProps.data)" />
                        <Button v-if="showEdit" icon="pi pi-pencil" rounded
                            class="w-9! h-9! bg-blue-50! dark:bg-blue-500/10! text-blue-600! dark:text-blue-400! border-blue-200! dark:border-blue-500/30! hover:bg-blue-100! dark:hover:bg-blue-500/20! transition-colors"
                            @click="emit('edit', slotProps.data)" />
                        <Button v-if="showDelete" icon="pi pi-trash" rounded
                            class="w-9! h-9! bg-red-50! dark:bg-red-500/10! text-red-600! dark:text-red-400! border-red-200! dark:border-red-500/30! hover:bg-red-100! dark:hover:bg-red-500/20! transition-colors"
                            @click="emit('delete', slotProps.data)" />
                    </div>
                </template>
            </Column>
        </DataTable>
    </div>
</template>

<style scoped>
@reference "../../css/app.css";

/* Full screen datatable with fixed paginator */
.datatable-fullscreen {
    display: flex;
    flex-direction: column;
    height: 100%;
    min-height: 0;
}

:deep(.p-datatable) {
    display: flex;
    flex-direction: column;
    height: 100%;
}

:deep(.p-datatable-table-container) {
    flex: 1;
    overflow: auto;
    min-height: 0;
}

:deep(.p-paginator) {
    @apply bg-slate-50 dark:bg-slate-800 border-t border-slate-200 dark:border-slate-700 py-3 px-4;
    flex-shrink: 0;
    position: sticky;
    bottom: 0;
}

/* Table header styling */
:deep(.p-datatable-thead > tr > th) {
    @apply bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-400;
    @apply font-semibold text-xs uppercase tracking-wider py-4 px-5;
    @apply border-b border-slate-200 dark:border-slate-700;
}

/* Table body styling */
:deep(.p-datatable-tbody > tr) {
    @apply bg-white dark:bg-slate-900 transition-colors duration-200;
}

:deep(.p-datatable-tbody > tr:hover) {
    @apply bg-slate-50 dark:bg-slate-800;
}

:deep(.p-datatable-tbody > tr > td) {
    @apply py-4 px-5 border-b border-slate-100 dark:border-slate-800;
    @apply text-slate-700 dark:text-slate-300;
}

/* Paginator buttons */
:deep(.p-paginator .p-paginator-page) {
    @apply min-w-10 h-10 rounded-lg font-medium;
}

:deep(.p-paginator .p-paginator-page.p-highlight) {
    @apply bg-primary-500 text-white;
}
</style>
