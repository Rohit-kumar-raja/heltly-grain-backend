<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps<{
    status?: string;
}>();

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout>

        <Head title="Forgot Password" />

        <!-- Header -->
        <div class="text-center mb-8">
            <div
                class="w-16 h-16 mx-auto mb-4 rounded-full bg-primary-100 dark:bg-primary-500/20 flex items-center justify-center">
                <i class="pi pi-lock text-3xl text-primary-500"></i>
            </div>
            <h2 class="text-2xl font-bold text-slate-900 dark:text-slate-100">Forgot Password?</h2>
            <p class="text-slate-500 dark:text-slate-400 mt-2 text-sm">
                No worries! Enter your email and we'll send you a reset link.
            </p>
        </div>

        <!-- Success Message -->
        <div v-if="status"
            class="mb-6 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 text-emerald-700 dark:text-emerald-400 text-sm">
            <i class="pi pi-check-circle mr-2"></i>{{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-5">
            <!-- Email Field -->
            <div class="space-y-2">
                <label for="email"
                    class="flex items-center gap-2 text-sm font-semibold text-slate-700 dark:text-slate-300">
                    <i class="pi pi-envelope text-primary-500"></i>
                    Email Address
                </label>
                <InputText id="email" type="email" v-model="form.email" required autofocus autocomplete="username"
                    placeholder="Enter your registered email"
                    class="w-full! rounded-xl! border-slate-200! dark:border-slate-600! focus:border-primary-500! transition-colors!"
                    :invalid="!!form.errors.email" />
                <small v-if="form.errors.email" class="text-red-500 text-xs flex items-center gap-1">
                    <i class="pi pi-exclamation-circle"></i> {{ form.errors.email }}
                </small>
            </div>

            <!-- Submit Button -->
            <Button type="submit" label="Send Reset Link" icon="pi pi-send" :loading="form.processing"
                class="w-full! justify-center! bg-primary-500! hover:bg-primary-600! border-primary-500! rounded-xl! py-3! text-base! font-semibold! shadow-lg! shadow-primary-500/30!" />

            <!-- Back to Login -->
            <div class="text-center mt-6">
                <Link :href="route('login')"
                    class="inline-flex items-center gap-2 text-slate-600 dark:text-slate-400 hover:text-primary-600 dark:hover:text-primary-400 text-sm font-medium transition-colors">
                    <i class="pi pi-arrow-left text-xs"></i>
                    Back to Sign In
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>
