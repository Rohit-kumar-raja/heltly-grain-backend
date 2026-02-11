<script setup lang="ts">
import Button from 'primevue/button';
import Breadcrumb from 'primevue/breadcrumb';
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { useSidebar } from '@/Composables/useSidebar';
import { useTheme } from '@/Composables/useTheme';

// Import separate components
import NotificationDropdown from './NotificationDropdown.vue';
import QuickActions from './QuickActions.vue';
import UserDropdown from './UserDropdown.vue';

interface User {
    name: string;
    email: string;
}

const props = defineProps<{
    user: User | null;
    pageTitle?: string;
}>();

const { sidebarCollapsed, toggleSidebar } = useSidebar();
const { isDark, toggleDarkMode } = useTheme();

// Breadcrumb configuration
const home = {
    icon: 'pi pi-home',
    command: () => router.visit(route('dashboard'))
};

const breadcrumbItems = computed(() => {
    if (!props.pageTitle || props.pageTitle === 'dashboard') return [];
    return [
        {
            label: props.pageTitle?.charAt(0).toUpperCase() + props.pageTitle?.slice(1),
        }
    ];
});
</script>

<template>
    <header
        class="sticky top-0 z-30 h-16 flex items-center justify-between px-4 lg:px-6 bg-white/95 dark:bg-slate-900 backdrop-blur-md border-b border-slate-200 dark:border-slate-700 shadow-sm">

        <!-- Left Section -->
        <div class="flex items-center gap-2">
            <!-- Sidebar Toggle Button -->
            <Button :icon="sidebarCollapsed ? 'pi pi-chevron-right' : 'pi pi-bars'" text rounded @click="toggleSidebar"
                class="text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800" />

            <!-- Breadcrumb Navigation -->
            <div class="flex items-center gap-2">
                <Breadcrumb :home="home" :model="breadcrumbItems" :pt="{
                    root: { class: 'bg-transparent! border-0! p-0!' },
                    item: { class: 'text-sm!' },
                    separator: { class: 'mx-2! text-slate-400!' }
                }" />
            </div>
        </div>

        <!-- Right Section -->
        <div class="flex items-center gap-1 sm:gap-2">
            <!-- Quick Actions Component -->
            <QuickActions />

            <!-- Divider -->
            <div class="hidden sm:block w-px h-8 bg-slate-200 dark:bg-slate-700 mx-2"></div>

            <!-- Theme Toggle -->
            <Button :icon="isDark ? 'pi pi-sun' : 'pi pi-moon'" v-tooltip.bottom="isDark ? 'Light Mode' : 'Dark Mode'"
                text rounded @click="toggleDarkMode"
                class="text-slate-600! dark:text-slate-300! hover:bg-slate-100! dark:hover:bg-slate-800!" />

            <!-- Notifications Component -->
            <NotificationDropdown />

            <!-- Divider -->
            <div class="w-px h-8 bg-slate-200 dark:bg-slate-700 mx-1 sm:mx-2"></div>

            <!-- User Dropdown Component -->
            <UserDropdown :user="user" />
        </div>
    </header>
</template>
