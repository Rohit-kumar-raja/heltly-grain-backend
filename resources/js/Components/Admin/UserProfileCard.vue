<script setup lang="ts">
import { useSidebar } from '@/Composables/useSidebar';

interface User {
    name: string;
    email: string;
}

defineProps<{
    user: User | null;
    collapsed?: boolean;
}>();

const { sidebarCollapsed } = useSidebar();
</script>

<template>
    <div class="p-3 border-t border-slate-200 dark:border-slate-700">
        <div class="flex items-center gap-3 px-3 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800"
            :class="(collapsed || sidebarCollapsed) ? 'justify-center' : ''">
            <div
                class="w-9 h-9 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white font-semibold text-sm shadow-md shrink-0">
                {{ user ? user.name[0].toUpperCase() : 'U' }}
            </div>
            <div v-if="!collapsed && !sidebarCollapsed" class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-slate-900 dark:text-slate-100 truncate">
                    {{ user ? user.name : 'User' }}
                </p>
                <p class="text-xs text-slate-500 dark:text-slate-400 truncate">
                    {{ user ? user.email : 'user@example.com' }}
                </p>
            </div>
        </div>
    </div>
</template>
