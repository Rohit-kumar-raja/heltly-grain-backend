<script setup lang="ts">
import Button from 'primevue/button';
import SidebarBrand from './SidebarBrand.vue';
import SidebarNav from './SidebarNav.vue';
import UserProfileCard from './UserProfileCard.vue';
import { useSidebar } from '@/Composables/useSidebar';

interface NavItem {
    label: string;
    icon: string;
    route: string;
}

interface User {
    name: string;
    email: string;
}

defineProps<{
    navItems: NavItem[];
    user: User | null;
    brandTitle?: string;
}>();

const { sidebarCollapsed, mobileSidebarOpen, closeMobileSidebar } = useSidebar();
</script>

<template>
    <!-- Desktop Sidebar -->
    <aside
        class="hidden lg:flex flex-col border-r border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 transition-all duration-300 ease-in-out"
        :class="sidebarCollapsed ? 'w-20' : 'w-64'">

        <SidebarBrand :title="brandTitle" />
        <SidebarNav :items="navItems" />
        <UserProfileCard :user="user" />
    </aside>

    <!-- Mobile Sidebar Overlay -->
    <Transition name="fade">
        <div v-if="mobileSidebarOpen" class="fixed inset-0 z-40 bg-black/60 backdrop-blur-sm lg:hidden"
            @click="closeMobileSidebar"></div>
    </Transition>

    <!-- Mobile Sidebar -->
    <Transition name="slide">
        <aside v-if="mobileSidebarOpen"
            class="fixed inset-y-0 left-0 z-50 w-72 flex flex-col bg-white dark:bg-slate-900 shadow-2xl lg:hidden">

            <!-- Mobile Header -->
            <div class="h-16 flex items-center justify-between border-b border-slate-200 dark:border-slate-700 px-4">
                <div class="flex items-center gap-3">
                    <img :src="asset('assets/logo.png')" alt="HealthyGain" class="h-10 w-auto" />
                </div>
                <Button icon="pi pi-times" text rounded size="small" @click="closeMobileSidebar"
                    class="text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200" />
            </div>

            <!-- Mobile Navigation -->
            <SidebarNav :items="navItems" :collapsed="false" />

            <!-- Mobile User Section -->
            <UserProfileCard :user="user" :collapsed="false" />
        </aside>
    </Transition>
</template>

<style scoped>
.slide-enter-active,
.slide-leave-active {
    transition: transform 0.3s ease;
}

.slide-enter-from,
.slide-leave-to {
    transform: translateX(-100%);
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
