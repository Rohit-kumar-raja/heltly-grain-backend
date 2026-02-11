<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputText from 'primevue/inputtext';
import Password from 'primevue/password';
import Button from 'primevue/button';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    email: string;
    token: string;
}>();

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => {
            form.reset('password', 'password_confirmation');
        },
    });
};
</script>

<template>
    <GuestLayout>

        <Head title="Reset Password" />

        <!-- Header -->
        <div class="text-center mb-8">
            <div
                class="w-16 h-16 mx-auto mb-4 rounded-full bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center">
                <i class="pi pi-key text-3xl text-emerald-500"></i>
            </div>
            <h2 class="text-2xl font-bold text-slate-900 dark:text-slate-100">Reset Password</h2>
            <p class="text-slate-500 dark:text-slate-400 mt-2 text-sm">
                Create a new secure password for your account.
            </p>
        </div>

        <form @submit.prevent="submit" class="space-y-5">
            <!-- Email Field (readonly) -->
            <div class="space-y-2">
                <label for="email"
                    class="flex items-center gap-2 text-sm font-semibold text-slate-700 dark:text-slate-300">
                    <i class="pi pi-envelope text-primary-500"></i>
                    Email Address
                </label>
                <InputText id="email" type="email" v-model="form.email" required autofocus autocomplete="username"
                    class="w-full! rounded-xl! border-slate-200! dark:border-slate-600! bg-slate-50! dark:bg-slate-700! cursor-not-allowed!"
                    disabled />
                <small v-if="form.errors.email" class="text-red-500 text-xs flex items-center gap-1">
                    <i class="pi pi-exclamation-circle"></i> {{ form.errors.email }}
                </small>
            </div>

            <!-- New Password Field -->
            <div class="space-y-2">
                <label for="password"
                    class="flex items-center gap-2 text-sm font-semibold text-slate-700 dark:text-slate-300">
                    <i class="pi pi-lock text-primary-500"></i>
                    New Password
                </label>
                <Password id="password" v-model="form.password" required autocomplete="new-password"
                    placeholder="Enter new password" toggleMask
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
                    autocomplete="new-password" placeholder="Confirm new password" toggleMask :feedback="false"
                    inputClass="w-full! rounded-xl! border-slate-200! dark:border-slate-600! focus:border-primary-500! transition-colors!"
                    class="w-full!" :invalid="!!form.errors.password_confirmation" />
                <small v-if="form.errors.password_confirmation" class="text-red-500 text-xs flex items-center gap-1">
                    <i class="pi pi-exclamation-circle"></i> {{ form.errors.password_confirmation }}
                </small>
            </div>

            <!-- Submit Button -->
            <Button type="submit" label="Reset Password" icon="pi pi-check" :loading="form.processing"
                class="w-full! justify-center! bg-primary-500! hover:bg-primary-600! border-primary-500! rounded-xl! py-3! text-base! font-semibold! shadow-lg! shadow-primary-500/30!" />
        </form>
    </GuestLayout>
</template>
