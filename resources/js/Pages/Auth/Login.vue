<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputText from 'primevue/inputtext';
import Password from 'primevue/password';
import Checkbox from 'primevue/checkbox';
import Button from 'primevue/button';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps<{
    canResetPassword?: boolean;
    status?: string;
}>();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => {
            form.reset('password');
        },
    });
};
</script>

<template>
    <GuestLayout>

        <Head title="Log in" />

        <!-- Header -->
        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-slate-900 dark:text-slate-100">Welcome Back!</h2>
            <p class="text-slate-500 dark:text-slate-400 mt-2">Sign in to continue to your account</p>
        </div>

        <!-- Status Message -->
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
                    placeholder="Enter your email"
                    class="w-full! rounded-xl! border-slate-200! dark:border-slate-600! focus:border-primary-500! transition-colors!"
                    :invalid="!!form.errors.email" />
                <small v-if="form.errors.email" class="text-red-500 text-xs flex items-center gap-1">
                    <i class="pi pi-exclamation-circle"></i> {{ form.errors.email }}
                </small>
            </div>

            <!-- Password Field -->
            <div class="space-y-2">
                <label for="password"
                    class="flex items-center gap-2 text-sm font-semibold text-slate-700 dark:text-slate-300">
                    <i class="pi pi-lock text-primary-500"></i>
                    Password
                </label>
                <Password id="password" v-model="form.password" required autocomplete="current-password"
                    placeholder="Enter your password" toggleMask :feedback="false"
                    inputClass="w-full! rounded-xl! border-slate-200! dark:border-slate-600! focus:border-primary-500! transition-colors!"
                    class="w-full!" :invalid="!!form.errors.password" />
                <small v-if="form.errors.password" class="text-red-500 text-xs flex items-center gap-1">
                    <i class="pi pi-exclamation-circle"></i> {{ form.errors.password }}
                </small>
            </div>

            <!-- Remember Me & Forgot Password -->
            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 cursor-pointer">
                    <Checkbox v-model="form.remember" :binary="true" inputId="remember" />
                    <span class="text-sm text-slate-600 dark:text-slate-400">Remember me</span>
                </label>

                <Link v-if="canResetPassword" :href="route('password.request')"
                    class="text-sm text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 font-medium">
                    Forgot password?
                </Link>
            </div>

            <!-- Submit Button -->
            <Button type="submit" label="Sign In" icon="pi pi-sign-in" :loading="form.processing"
                class="w-full! justify-center! bg-primary-500! hover:bg-primary-600! border-primary-500! rounded-xl! py-3! text-base! font-semibold! shadow-lg! shadow-primary-500/30! mt-2!" />

            <!-- Help Text -->
            <p class="text-center text-xs text-slate-500 dark:text-slate-400 mt-6">
                Need help? Contact your administrator for access.
            </p>
        </form>
    </GuestLayout>
</template>
