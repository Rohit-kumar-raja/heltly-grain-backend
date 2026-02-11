<script setup lang="ts">
import Password from 'primevue/password';
import Button from 'primevue/button';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const passwordInput = ref<HTMLInputElement | null>(null);
const currentPasswordInput = ref<HTMLInputElement | null>(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value?.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value?.focus();
            }
        },
    });
};
</script>

<template>
    <section>
        <header class="mb-6">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-500/20 flex items-center justify-center">
                    <i class="pi pi-lock text-blue-600 dark:text-blue-400"></i>
                </div>
                <h2 class="text-lg font-bold text-slate-900 dark:text-slate-100">
                    Update Password
                </h2>
            </div>
            <p class="text-sm text-slate-500 dark:text-slate-400">
                Ensure your account is using a long, random password to stay secure.
            </p>
        </header>

        <form @submit.prevent="updatePassword" class="space-y-5">
            <div class="space-y-2">
                <label for="current_password"
                    class="flex items-center gap-2 text-sm font-semibold text-slate-700 dark:text-slate-300">
                    <i class="pi pi-key text-blue-500 text-xs"></i>
                    Current Password
                </label>
                <Password id="current_password" ref="currentPasswordInput" v-model="form.current_password"
                    placeholder="Enter current password" toggleMask :feedback="false"
                    inputClass="w-full! rounded-xl! border-slate-200! dark:border-slate-600! focus:border-primary-500! transition-colors!"
                    class="w-full!" :invalid="!!form.errors.current_password" />
                <small v-if="form.errors.current_password" class="text-red-500 text-xs flex items-center gap-1">
                    <i class="pi pi-exclamation-circle"></i> {{ form.errors.current_password }}
                </small>
            </div>

            <div class="space-y-2">
                <label for="password"
                    class="flex items-center gap-2 text-sm font-semibold text-slate-700 dark:text-slate-300">
                    <i class="pi pi-lock text-blue-500 text-xs"></i>
                    New Password
                </label>
                <Password id="password" ref="passwordInput" v-model="form.password" placeholder="Enter new password"
                    toggleMask promptLabel="Choose a password" weakLabel="Too simple" mediumLabel="Average complexity"
                    strongLabel="Strong password"
                    inputClass="w-full! rounded-xl! border-slate-200! dark:border-slate-600! focus:border-primary-500! transition-colors!"
                    class="w-full!" :invalid="!!form.errors.password" />
                <small v-if="form.errors.password" class="text-red-500 text-xs flex items-center gap-1">
                    <i class="pi pi-exclamation-circle"></i> {{ form.errors.password }}
                </small>
            </div>

            <div class="space-y-2">
                <label for="password_confirmation"
                    class="flex items-center gap-2 text-sm font-semibold text-slate-700 dark:text-slate-300">
                    <i class="pi pi-lock text-blue-500 text-xs"></i>
                    Confirm New Password
                </label>
                <Password id="password_confirmation" v-model="form.password_confirmation"
                    placeholder="Confirm new password" toggleMask :feedback="false"
                    inputClass="w-full! rounded-xl! border-slate-200! dark:border-slate-600! focus:border-primary-500! transition-colors!"
                    class="w-full!" :invalid="!!form.errors.password_confirmation" />
                <small v-if="form.errors.password_confirmation" class="text-red-500 text-xs flex items-center gap-1">
                    <i class="pi pi-exclamation-circle"></i> {{ form.errors.password_confirmation }}
                </small>
            </div>

            <div class="flex items-center gap-4 pt-2">
                <Button type="submit" label="Update Password" icon="pi pi-refresh" :loading="form.processing"
                    class="bg-blue-500! hover:bg-blue-600! border-blue-500! rounded-xl! px-5!" />

                <Transition enter-active-class="transition ease-in-out" enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out" leave-to-class="opacity-0">
                    <span v-if="form.recentlySuccessful"
                        class="inline-flex items-center gap-1.5 text-sm text-emerald-600 dark:text-emerald-400">
                        <i class="pi pi-check-circle"></i> Password updated
                    </span>
                </Transition>
            </div>
        </form>
    </section>
</template>
