<script setup>
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
import { computed } from 'vue';

const props = defineProps({
    visible: { type: Boolean, default: false },
    title: { type: String, default: 'Confirm Action' },
    message: { type: String, default: 'Are you sure you want to proceed?' },
    itemName: { type: String, default: '' },
    type: { type: String, default: 'danger' }, // danger, warning, info
    confirmLabel: { type: String, default: 'Confirm' },
    cancelLabel: { type: String, default: 'Cancel' },
    loading: { type: Boolean, default: false },
    width: { type: String, default: '420px' },
});

const emit = defineEmits(['update:visible', 'confirm', 'cancel']);

const dialogVisible = computed({
    get: () => props.visible,
    set: (value) => emit('update:visible', value)
});

const typeConfig = computed(() => {
    const configs = {
        danger: {
            icon: 'pi-trash',
            bgClass: 'bg-red-100 dark:bg-red-500/20',
            iconClass: 'text-red-500',
            buttonClass: 'bg-red-500! hover:bg-red-600! border-red-500!'
        },
        warning: {
            icon: 'pi-exclamation-triangle',
            bgClass: 'bg-amber-100 dark:bg-amber-500/20',
            iconClass: 'text-amber-500',
            buttonClass: 'bg-amber-500! hover:bg-amber-600! border-amber-500!'
        },
        info: {
            icon: 'pi-info-circle',
            bgClass: 'bg-blue-100 dark:bg-blue-500/20',
            iconClass: 'text-blue-500',
            buttonClass: 'bg-blue-500! hover:bg-blue-600! border-blue-500!'
        }
    };
    return configs[props.type] || configs.danger;
});

const hideDialog = () => {
    emit('update:visible', false);
    emit('cancel');
};

const onConfirm = () => {
    emit('confirm');
};
</script>

<template>
    <Dialog v-model:visible="dialogVisible" :style="{ width: width }" :modal="true"
        :pt="{ root: { class: 'rounded-2xl! overflow-hidden border-0! shadow-2xl!' }, header: { class: 'hidden!' }, content: { class: 'p-0!' }, footer: { class: 'p-0!' } }">

        <!-- Content -->
        <div class="p-6 text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center"
                :class="typeConfig.bgClass">
                <i :class="['pi', typeConfig.icon, 'text-3xl', typeConfig.iconClass]"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-900 dark:text-slate-100 mb-2">{{ title }}</h3>
            <p class="text-slate-600 dark:text-slate-400 mb-1">{{ message }}</p>
            <p v-if="itemName" class="text-slate-500 dark:text-slate-400">
                <span class="font-semibold text-slate-700 dark:text-slate-300">"{{ itemName }}"</span> will be
                permanently removed.
            </p>
            <slot></slot>
        </div>

        <!-- Footer -->
        <div
            class="px-6 py-4 bg-slate-50 dark:bg-slate-800 border-t border-slate-200 dark:border-slate-700 flex justify-center gap-3">
            <Button :label="cancelLabel" icon="pi pi-times" @click="hideDialog" text
                class="text-slate-600! dark:text-slate-400! hover:bg-slate-100! dark:hover:bg-slate-700! rounded-xl! px-6!" />
            <Button :label="confirmLabel" :icon="'pi ' + typeConfig.icon" @click="onConfirm" :loading="loading"
                :class="[typeConfig.buttonClass, 'rounded-xl! shadow-lg! px-6!']" />
        </div>
    </Dialog>
</template>
