<script setup lang="ts">
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
import { computed } from 'vue';

const props = defineProps({
    visible: { type: Boolean, default: false },
    title: { type: String, default: 'Dialog' },
    subtitle: { type: String, default: '' },
    icon: { type: String, default: 'pi-box' },
    width: { type: String, default: '900px' },
    loading: { type: Boolean, default: false },
    submitLabel: { type: String, default: 'Save' },
    cancelLabel: { type: String, default: 'Cancel' },
    submitIcon: { type: String, default: 'pi pi-check' },
    showFooter: { type: Boolean, default: true },
});

const emit = defineEmits(['update:visible', 'submit', 'cancel']);

const dialogVisible = computed({
    get: () => props.visible,
    set: (value) => emit('update:visible', value)
});

const hideDialog = () => {
    emit('update:visible', false);
    emit('cancel');
};

const onSubmit = () => {
    emit('submit');
};
</script>

<template>
    <Dialog v-model:visible="dialogVisible" :style="{ width: width }" :modal="true" :dismissableMask="true"
        :draggable="false" :closable="false"
        :pt="{ root: { class: 'rounded-2xl! overflow-hidden border-0! shadow-2xl!' }, header: { class: 'p-0!' } }">

        <!-- Custom Header -->
        <template #header>
            <div
                class="w-full px-6 py-5 bg-linear-to-r from-primary-500 to-primary-600 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                        <i :class="['pi', icon, 'text-white text-lg']"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-white m-0">{{ title }}</h2>
                        <p v-if="subtitle" class="text-primary-100 text-sm m-0">{{ subtitle }}</p>
                    </div>
                </div>
                <Button icon="pi pi-times" rounded text class="text-white! hover:bg-white/20! w-10! h-10! shrink-0"
                    @click="hideDialog" />
            </div>
        </template>

        <!-- Content -->
        <div class="p-6 bg-white dark:bg-slate-900">
            <slot></slot>
        </div>

        <!-- Footer -->
        <template #footer v-if="showFooter">
            <div
                class="px-6 w-full flex justify-end py-3 bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 gap-3">
                <Button :label="cancelLabel" icon="pi pi-times" @click="hideDialog" text
                    class="text-slate-600! dark:text-slate-400! hover:bg-slate-100! dark:hover:bg-slate-700! rounded-xl!" />
                <Button :label="submitLabel" :icon="submitIcon" @click="onSubmit" :loading="loading"
                    class="bg-primary-500! hover:bg-primary-600! border-primary-500! rounded-xl! shadow-lg!" />
            </div>
        </template>
    </Dialog>
</template>
