<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import { Head, usePage } from '@inertiajs/vue3';

defineProps<{
    mustVerifyEmail?: boolean;
    status?: string;
}>();

const user = usePage().props.auth.user;

// Get user initials for avatar
const getUserInitials = () => {
    if (!user?.name) return 'U';
    const parts = user.name.split(' ');
    if (parts.length >= 2) {
        return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
    }
    return parts[0][0].toUpperCase();
};
</script>

<template>

    <Head title="Profile Settings" />

    <AdminLayout>
        <div class="max-w-5xl mx-auto space-y-6">
            <!-- Profile Header Card -->
            <div
                class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                <!-- Banner -->
                <div class="h-32 bg-linear-to-r from-primary-500 via-primary-600 to-emerald-500 relative">
                    <div class="absolute inset-0 opacity-20"
                        style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.4\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');">
                    </div>
                </div>

                <!-- Profile Info -->
                <div class="px-6 pb-6 w-full!">
                    <div class="flex flex-col sm:flex-row sm:items-end gap-4 mt-3">
                        <!-- Avatar -->
                        <div
                            class="w-16 h-16 rounded-2xl bg-linear-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white font-bold text-3xl shadow-xl ring-4 ring-white dark:ring-slate-800">
                            {{ getUserInitials() }}
                        </div>

                        <!-- User Details -->
                        <div class="flex-1 pt-2 sm:pt-0 ">
                            <h1 class="text-2xl font-bold text-slate-900 dark:text-slate-100">
                                {{ user?.name || 'User' }}
                            </h1>
                            <p class="text-slate-500 dark:text-slate-400 flex items-center gap-2 mt-1">
                                <i class="pi pi-envelope text-sm"></i>
                                {{ user?.email || '' }}
                            </p>
                        </div>

                        <!-- Status Badge -->
                        <div class="flex items-center gap-2">
                            <span
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-400 text-sm font-medium">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                Administrator
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settings Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Profile Information -->
                <div
                    class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
                    <UpdateProfileInformationForm :must-verify-email="mustVerifyEmail" :status="status" />
                </div>

                <!-- Update Password -->
                <div
                    class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
                    <UpdatePasswordForm />
                </div>
            </div>

            <!-- Danger Zone -->
            <div
                class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-red-200 dark:border-red-500/30 p-6">
                <DeleteUserForm />
            </div>
        </div>
    </AdminLayout>
</template>
