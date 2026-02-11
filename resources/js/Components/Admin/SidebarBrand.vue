<script setup lang="ts">
import { useSidebar } from '@/Composables/useSidebar';

defineProps<{
    title?: string;
}>();

const { sidebarCollapsed } = useSidebar();
</script>

<template>
    <div class="h-16 flex items-center border-b border-slate-200 dark:border-slate-700 px-4"
        :class="sidebarCollapsed ? 'justify-center' : 'justify-between'">
        <div class="flex items-center gap-3">
            <!-- Logo Image -->
            <img src="/assets/logo.png" alt="HealthyGain"
                :class="sidebarCollapsed ? 'w-10 h-10 object-contain' : 'h-10 w-auto'" />
        </div>
        <slot />
    </div>
</template>
