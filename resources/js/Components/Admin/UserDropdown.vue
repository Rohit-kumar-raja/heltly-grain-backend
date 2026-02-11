<script setup lang="ts">
import Menu from 'primevue/menu';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

interface User {
    name: string;
    email: string;
}

const props = defineProps<{
    user: User | null;
}>();

const userMenu = ref();

const menuItems = ref([
    {
        label: 'Account',
        items: [
            {
                label: 'My Profile',
                icon: 'pi pi-user',
                command: () => router.visit(route('profile.edit'))
            },
            {
                label: 'Settings',
                icon: 'pi pi-cog',
                command: () => router.visit(route('profile.edit'))
            }
        ]
    },
    {
        separator: true
    },
    {
        label: 'Logout',
        icon: 'pi pi-sign-out',
        command: () => router.post(route('logout'))
    }
]);

const toggleUserMenu = (event: Event) => {
    userMenu.value.toggle(event);
};

const getUserInitials = () => {
    if (!props.user?.name) return 'U';
    const parts = props.user.name.split(' ');
    if (parts.length >= 2) {
        return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
    }
    return parts[0][0].toUpperCase();
};
</script>

<template>
    <div class="relative">
        <button @click="toggleUserMenu"
            class="flex items-center gap-2 p-1 sm:p-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer group">
            <!-- Avatar -->
            <div
                class="w-9 h-9 rounded-full bg-linear-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white font-semibold text-sm shadow-lg shadow-primary-500/20 ring-2 ring-white dark:ring-slate-800 group-hover:ring-primary-200 dark:group-hover:ring-primary-800 transition-all">
                {{ getUserInitials() }}
            </div>
            <!-- User Info (Desktop) -->
            <div class="hidden lg:flex flex-col items-start">
                <span class="text-sm font-medium text-slate-700 dark:text-slate-200">
                    {{ user?.name || 'User' }}
                </span>
                <span class="text-xs text-slate-500 dark:text-slate-400">
                    Administrator
                </span>
            </div>
            <!-- Dropdown Arrow -->
            <i
                class="pi pi-chevron-down text-xs text-slate-400 hidden lg:block group-hover:text-primary-500 transition-colors"></i>
        </button>

        <!-- Dropdown Menu -->
        <Menu ref="userMenu" :model="menuItems" :popup="true" />
    </div>
</template>
