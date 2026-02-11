<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputText from 'primevue/inputtext';
import Password from 'primevue/password';
import Button from 'primevue/button';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => {
            form.reset('password', 'password_confirmation');
        },
    });
};
</script>

<template>
    <GuestLayout>

        <Head title="Register" />

        <!-- Header -->
        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-slate-900 dark:text-slate-100">Create Account</h2>
            <p class="text-slate-500 dark:text-slate-400 mt-2">Start your wellness journey today</p>
        </div>

        <form @submit.prevent="submit" class="space-y-5">
            <!-- Name Field -->
            <div class="space-y-2">
                <label for="name"
                    class="flex items-center gap-2 text-sm font-semibold text-slate-700 dark:text-slate-300">
                    <i class="pi pi-user text-primary-500"></i>
                    Full Name
                </label>
                <InputText id="name" type="text" v-model="form.name" required autofocus autocomplete="name"
                    placeholder="Enter your full name"
                    class="w-full! rounded-xl! border-slate-200! dark:border-slate-600! focus:border-primary-500! transition-colors!"
                    :invalid="!!form.errors.name" />
                <small v-if="form.errors.name" class="text-red-500 text-xs flex items-center gap-1">
                    <i class="pi pi-exclamation-circle"></i> {{ form.errors.name }}
                </small>
            </div>

            <!-- Email Field -->
            <div class="space-y-2">
                <label for="email"
                    class="flex items-center gap-2 text-sm font-semibold text-slate-700 dark:text-slate-300">
                    <i class="pi pi-envelope text-primary-500"></i>
                    Email Address
                </label>
                <InputText id="email" type="email" v-model="form.email" required autocomplete="username"
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
                <Password id="password" v-model="form.password" required autocomplete="new-password"
                    placeholder="Create a strong password" toggleMask
                    inputClass="w-full! rounded-xl! border-slate-200! dark:border-slate-600! focus:border-primary-500! transition-colors!"
                    class="w-full!" :invalid="!!form.errors.password" promptLabel="Choose a password"
                    weakLabel="Too simple" mediumLabel="Average complexity" strongLabel="Strong password" />
                <small v-if="form.errors.password" class="text-red-500 text-xs flex items-center gap-1">
                    <i class="pi pi-exclamation-circle"></i> {{ form.errors.password }}
                </small>
            </div>

            <!-- Confirm Password Field -->
            <div class="space-y-2">
                <label for="password_confirmation"
                    class="flex items-center gap-2 text-sm font-semibold text-slate-700 dark:text-slate-300">
                    <i class="pi pi-lock text-primary-500"></i>
                    Confirm Password
                </label>
                <Password id="password_confirmation" v-model="form.password_confirmation" required
                    autocomplete="new-password" placeholder="Confirm your password" toggleMask :feedback="false"
                    inputClass="w-full! rounded-xl! border-slate-200! dark:border-slate-600! focus:border-primary-500! transition-colors!"
                    class="w-full!" :invalid="!!form.errors.password_confirmation" />
                <small v-if="form.errors.password_confirmation" class="text-red-500 text-xs flex items-center gap-1">
                    <i class="pi pi-exclamation-circle"></i> {{ form.errors.password_confirmation }}
                </small>
            </div>

            <!-- Terms Agreement -->
            <p class="text-xs text-slate-500 dark:text-slate-400 text-center">
                By creating an account, you agree to our
                <a href="#" class="text-primary-600 dark:text-primary-400 hover:underline">Terms of Service</a>
                and
                <a href="#" class="text-primary-600 dark:text-primary-400 hover:underline">Privacy Policy</a>
            </p>

            <!-- Submit Button -->
            <Button type="submit" label="Create Account" icon="pi pi-user-plus" :loading="form.processing"
                class="w-full! justify-center! bg-primary-500! hover:bg-primary-600! border-primary-500! rounded-xl! py-3! text-base! font-semibold! shadow-lg! shadow-primary-500/30!" />

            <!-- Divider -->
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-slate-200 dark:border-slate-700"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-4 bg-white dark:bg-slate-800 text-slate-500 dark:text-slate-400">or</span>
                </div>
            </div>

            <!-- Login Link -->
            <div class="text-center">
                <span class="text-slate-600 dark:text-slate-400 text-sm">Already have an account? </span>
                <Link :href="route('login')"
                    class="text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 font-semibold text-sm">
                    Sign In
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>
