<script setup lang="ts">
import { usePage, router } from '@inertiajs/vue3';
import AppSidebar from '@/Components/Admin/AppSidebar.vue';
import AppHeader from '@/Components/Admin/AppHeader.vue';
import { useSidebar } from '@/Composables/useSidebar';

// Navigation items configuration
const navItems = [
    { label: 'Dashboard', icon: 'pi pi-th-large', route: 'dashboard' },
    { label: 'Products', icon: 'pi pi-box', route: 'products.index' },
    { label: 'Orders', icon: 'pi pi-shopping-cart', route: 'orders.index' },
    { label: 'Wishlists', icon: 'pi pi-heart', route: 'wishlists.index' },
    { label: 'Smart Rules', icon: 'pi pi-cog', route: 'meal-plan.rules.index' },
    { label: 'App Users', icon: 'pi pi-users', route: 'app-users.index' },
    { label: 'Transactions', icon: 'pi pi-wallet', route: 'transactions.index' },
    { label: 'Categories', icon: 'pi pi-tags', route: 'categories.index' },
];

// Get current user from page props
const page = usePage();
const user = page.props.auth ? page.props.auth.user : null;

// Get current page title
const pageTitle = route().current()?.split('.')[0] || 'Dashboard';

// Close mobile sidebar on route change
const { closeMobileSidebar } = useSidebar();
router.on('navigate', () => {
    closeMobileSidebar();
});
</script>

<template>
    <div class="min-h-screen flex ">
        <!-- Sidebar Component -->
        <AppSidebar :nav-items="navItems" :user="user" brand-title="HealthyGain" />

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Header Component -->
            <AppHeader :user="user" :page-title="pageTitle" />

            <!-- Page Content -->
            <main class="flex-1 p-4 lg:p-6 overflow-x-hidden">
                <slot />
            </main>
        </div>
    </div>
</template>
