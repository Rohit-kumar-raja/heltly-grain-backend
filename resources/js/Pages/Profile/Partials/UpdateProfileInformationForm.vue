<script setup lang="ts">
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import { Link, useForm, usePage } from '@inertiajs/vue3';

defineProps<{
    mustVerifyEmail?: Boolean;
    status?: String;
}>();

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
});
</script>

<template>
    <section>
        <header class="mb-6">
            <div class="flex items-center gap-3 mb-2">
                <div
                    class="w-10 h-10 rounded-xl bg-primary-100 dark:bg-primary-500/20 flex items-center justify-center">
                    <i class="pi pi-user text-primary-600 dark:text-primary-400"></i>
                </div>
                <h2 class="text-lg font-bold text-slate-900 dark:text-slate-100">
                    Profile Information
                </h2>
            </div>
            <p class="text-sm text-slate-500 dark:text-slate-400">
                Update your account's profile information and email address.
            </p>
        </header>

        <form @submit.prevent="form.patch(route('profile.update'))" class="space-y-5">
            <div class="space-y-2">
                <label for="name"
                    class="flex items-center gap-2 text-sm font-semibold text-slate-700 dark:text-slate-300">
                    <i class="pi pi-id-card text-primary-500 text-xs"></i>
                    Full Name
                </label>
                <InputText id="name" type="text" v-model="form.name" required autofocus autocomplete="name"
                    placeholder="Enter your name"
                    class="w-full! rounded-xl! border-slate-200! dark:border-slate-600! focus:border-primary-500! transition-colors!"
                    :invalid="!!form.errors.name" />
                <small v-if="form.errors.name" class="text-red-500 text-xs flex items-center gap-1">
                    <i class="pi pi-exclamation-circle"></i> {{ form.errors.name }}
                </small>
            </div>

            <div class="space-y-2">
                <label for="email"
                    class="flex items-center gap-2 text-sm font-semibold text-slate-700 dark:text-slate-300">
                    <i class="pi pi-envelope text-primary-500 text-xs"></i>
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

            <div v-if="mustVerifyEmail && user.email_verified_at === null"
                class="p-4 rounded-xl bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/30">
                <p class="text-sm text-amber-700 dark:text-amber-400">
                    <i class="pi pi-exclamation-triangle mr-2"></i>
                    Your email address is unverified.
                    <Link :href="route('verification.send')" method="post" as="button"
                        class="font-semibold underline hover:text-amber-800 dark:hover:text-amber-300 ml-1">
                        Click here to re-send the verification email.
                    </Link>
                </p>

                <div v-show="status === 'verification-link-sent'"
                    class="mt-2 text-sm font-medium text-emerald-600 dark:text-emerald-400">
                    <i class="pi pi-check-circle mr-1"></i>
                    A new verification link has been sent to your email address.
                </div>
            </div>

            <div class="flex items-center gap-4 pt-2">
                <Button type="submit" label="Save Changes" icon="pi pi-check" :loading="form.processing"
                    class="bg-primary-500! hover:bg-primary-600! border-primary-500! rounded-xl! px-5!" />

                <Transition enter-active-class="transition ease-in-out" enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out" leave-to-class="opacity-0">
                    <span v-if="form.recentlySuccessful"
                        class="inline-flex items-center gap-1.5 text-sm text-emerald-600 dark:text-emerald-400">
                        <i class="pi pi-check-circle"></i> Saved successfully
                    </span>
                </Transition>
            </div>
        </form>
    </section>
</template>
