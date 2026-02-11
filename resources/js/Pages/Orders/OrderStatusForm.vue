<script setup>
import { ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Select from 'primevue/select';
import AppFormDialog from '@/Components/AppFormDialog.vue';
import { useToast } from 'primevue/usetoast';

const props = defineProps({
    visible: { type: Boolean, default: false },
    order: { type: Object, default: null },
});

const emit = defineEmits(['update:visible', 'saved']);

const toast = useToast();
const newStatus = ref('');

const statuses = [
    { label: 'Pending', value: 'pending' },
    { label: 'Processing', value: 'processing' },
    { label: 'Shipped', value: 'shipped' },
    { label: 'Delivered', value: 'delivered' },
    { label: 'Cancelled', value: 'cancelled' }
];

// Watch for order changes to populate status
watch(() => props.order, (order) => {
    if (order) {
        newStatus.value = order.status;
    }
}, { immediate: true });

const hideDialog = () => {
    emit('update:visible', false);
};

const updateStatus = () => {
    const form = useForm({
        status: newStatus.value
    });

    form.put(route('orders.update', props.order.id), {
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Successful', detail: 'Order Status Updated', life: 3000 });
            emit('update:visible', false);
            emit('saved');
        }
    });
};
</script>

<template>
    <AppFormDialog :visible="visible" @update:visible="$emit('update:visible', $event)" title="Update Status"
        subtitle="Change order status" icon="pi-flag" width="400px" submitLabel="Update" @submit="updateStatus"
        @cancel="hideDialog">
        <div class="space-y-4">
            <div class="space-y-2">
                <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 dark:text-slate-300">
                    <i class="pi pi-flag text-primary-500"></i>
                    Order Status
                </label>
                <Select v-model="newStatus" :options="statuses" optionLabel="label" optionValue="value"
                    placeholder="Select a Status" class="w-full! [&_.p-select]:rounded-xl!" />
            </div>
        </div>
    </AppFormDialog>
</template>
