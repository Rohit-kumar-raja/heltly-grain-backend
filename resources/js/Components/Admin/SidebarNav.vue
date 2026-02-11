<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { useSidebar } from '@/Composables/useSidebar';

interface NavItem {
    label: string;
    icon: string;
    route: string;
}

defineProps<{
    items: NavItem[];
    collapsed?: boolean;
}>();

const { sidebarCollapsed } = useSidebar();
</script>

<template>
    <nav class="flex-1 p-3 space-y-1 overflow-y-auto">
        <Link v-for="item in items" :key="item.label" :href="item.route ? route(item.route) : '#'"
            class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 group relative" :class="[
                $page.url && (route().current(item.route) || route().current(item.route.replace('.index', '.*')))
                    ? 'bg-primary-500 text-white shadow-lg shadow-primary-500/30'
                    : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-slate-100'
            ]" :title="collapsed || sidebarCollapsed ? item.label : ''">
            <i :class="[item.icon, 'text-lg']" :style="{ minWidth: '24px', textAlign: 'center' }"></i>
            <span v-if="!collapsed && !sidebarCollapsed" class="font-medium">{{ item.label }}</span>

            <!-- Tooltip for collapsed state -->
            <div v-if="collapsed || sidebarCollapsed"
                class="absolute left-full ml-2 px-3 py-1.5 bg-slate-900 dark:bg-slate-700 text-white text-sm rounded-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all whitespace-nowrap z-50 shadow-xl">
                {{ item.label }}
            </div>
        </Link>
    </nav>
</template>
