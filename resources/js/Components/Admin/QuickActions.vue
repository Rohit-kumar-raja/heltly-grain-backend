<script setup lang="ts">
import Popover from 'primevue/popover';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const quickActionsPanel = ref();

const actions = ref([

    {
        label: 'Products',
        description: 'Manage product inventory',
        icon: 'pi pi-box',
        color: 'bg-blue-500',
        route: 'products.index'
    },
    {
        label: 'Orders',
        description: 'View and manage orders',
        icon: 'pi pi-shopping-cart',
        color: 'bg-purple-500',
        route: 'orders.index'
    },
    {
        label: 'Dashboard',
        description: 'View analytics & reports',
        icon: 'pi pi-chart-bar',
        color: 'bg-amber-500',
        route: 'dashboard'
    },
    {
        label: 'Settings',
        description: 'Account & preferences',
        icon: 'pi pi-cog',
        color: 'bg-slate-500',
        route: 'profile.edit'
    }
]);

const togglePanel = (event: Event) => {
    quickActionsPanel.value.toggle(event);
};

const executeAction = (action: Record<string, string>) => {
    if (action.route) {
        router.visit(route(action.route));
    }
    quickActionsPanel.value.hide();
};
</script>

<template>
    <div class="relative flex items-center gap-1">
        <!-- Desktop Quick Actions Bar -->
        <div class="hidden sm:flex items-center gap-1 bg-slate-100 dark:bg-slate-800 rounded-xl p-1">
            <!-- Quick Actions Menu -->


            <!-- Add Product Shortcut -->
            <button @click="router.visit(route('app-users.index'))" v-tooltip.bottom="'Users'"
                class="w-8 h-8 flex items-center justify-center rounded-lg text-emerald-600 dark:text-emerald-400 hover:bg-white dark:hover:bg-slate-700 transition-colors">
                <i class="pi pi-user text-sm"></i>
            </button>

            <!-- Orders Shortcut -->
            <button @click="router.visit(route('orders.index'))" v-tooltip.bottom="'Orders'"
                class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-600 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-700 transition-colors">
                <i class="pi pi-shopping-cart text-sm"></i>
            </button>

            <!-- Dashboard Shortcut -->
            <button @click="router.visit(route('dashboard'))" v-tooltip.bottom="'Dashboard'"
                class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-600 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-700 transition-colors">
                <i class="pi pi-chart-bar text-sm"></i>
            </button>
            <button @click="togglePanel" v-tooltip.bottom="'Quick Actions'"
                class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-600 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-700 transition-colors">
                <i class="pi pi-th-large text-sm"></i>
            </button>
        </div>

        <!-- Mobile Quick Actions Button -->
        <button @click="togglePanel" v-tooltip.bottom="'Quick Actions'"
            class="sm:hidden p-2 rounded-full text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
            <i class="pi pi-th-large text-lg"></i>
        </button>

        <!-- Quick Actions Popover -->
        <Popover ref="quickActionsPanel" :pt="{
            root: { class: 'rounded-2xl! shadow-2xl! border! border-slate-200! dark:border-slate-700! w-72! mt-2!' },
            content: { class: 'p-0!' }
        }">
            <!-- Header -->
            <div
                class="flex items-center gap-2 px-4 py-3 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 rounded-t-2xl">
                <i class="pi pi-bolt text-primary-500"></i>
                <span class="font-semibold text-slate-900 dark:text-slate-100">Quick Actions</span>
            </div>

            <!-- Actions Grid -->
            <div class="p-3 grid grid-cols-2 gap-2">
                <button v-for="action in actions" :key="action.label" @click="executeAction(action)"
                    class="flex flex-col items-center gap-2 p-3 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors group">
                    <div
                        :class="[action.color, 'w-10 h-10 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform']">
                        <i :class="[action.icon, 'text-white']"></i>
                    </div>
                    <span class="font-medium text-xs text-slate-700 dark:text-slate-200 text-center">
                        {{ action.label }}
                    </span>
                </button>
            </div>

            <!-- Footer -->
            <div
                class="px-4 py-3 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 rounded-b-2xl">
                <button @click="router.visit(route('dashboard'))"
                    class="w-full text-center text-xs text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 font-medium">
                    View Full Dashboard
                </button>
            </div>
        </Popover>
    </div>
</template>
