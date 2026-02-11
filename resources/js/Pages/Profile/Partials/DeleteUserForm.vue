<script setup lang="ts">
import Dialog from 'primevue/dialog';
import Password from 'primevue/password';
import Button from 'primevue/button';
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref<HTMLInputElement | null>(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;
    nextTick(() => passwordInput.value?.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value?.focus(),
        onFinish: () => {
            form.reset();
        },
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;
    form.clearErrors();
    form.reset();
};
</script>

<template>
    <section>
        <header class="mb-6">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-xl bg-red-100 dark:bg-red-500/20 flex items-center justify-center">
                    <i class="pi pi-exclamation-triangle text-red-600 dark:text-red-400"></i>
                </div>
                <h2 class="text-lg font-bold text-red-600 dark:text-red-400">
                    Danger Zone
                </h2>
            </div>
            <p class="text-sm text-slate-500 dark:text-slate-400">
                Once your account is deleted, all of its resources and data will be permanently deleted.
                Before deleting your account, please download any data or information that you wish to retain.
            </p>
        </header>

        <Button label="Delete Account" icon="pi pi-trash" severity="danger" @click="confirmUserDeletion"
            class="bg-red-500! hover:bg-red-600! border-red-500! rounded-xl!" />

        <!-- Delete Confirmation Dialog -->
        <Dialog v-model:visible="confirmingUserDeletion" modal header="Delete Account" :style="{ width: '28rem' }" :pt="{
            root: { class: 'rounded-2xl! overflow-hidden border-0! shadow-2xl!' },
            header: { class: 'bg-red-50! dark:bg-red-500/10! border-b! border-red-100! dark:border-red-500/20! px-6! py-4!' },
            headerTitle: { class: 'text-red-700! dark:text-red-400! font-bold!' },
            content: { class: 'p-6!' },
            closeButton: { class: 'text-red-500! hover:bg-red-100! dark:hover:bg-red-500/20!' }
        }">
            <div class="space-y-4">
                <div
                    class="w-16 h-16 mx-auto rounded-full bg-red-100 dark:bg-red-500/20 flex items-center justify-center mb-4">
                    <i class="pi pi-exclamation-triangle text-3xl text-red-500"></i>
                </div>

                <p class="text-center text-slate-600 dark:text-slate-400 text-sm">
                    Are you sure you want to delete your account? This action cannot be undone.
                    Please enter your password to confirm.
                </p>

                <div class="space-y-2">
                    <label for="delete_password"
                        class="flex items-center gap-2 text-sm font-semibold text-slate-700 dark:text-slate-300">
                        <i class="pi pi-lock text-red-500 text-xs"></i>
                        Password
                    </label>
                    <Password id="delete_password" ref="passwordInput" v-model="form.password"
                        placeholder="Enter your password to confirm" toggleMask :feedback="false"
                        inputClass="w-full! rounded-xl! border-slate-200! dark:border-slate-600! focus:border-red-500! transition-colors!"
                        class="w-full!" :invalid="!!form.errors.password" @keyup.enter="deleteUser" />
                    <small v-if="form.errors.password" class="text-red-500 text-xs flex items-center gap-1">
                        <i class="pi pi-exclamation-circle"></i> {{ form.errors.password }}
                    </small>
                </div>
            </div>

            <template #footer>
                <div class="flex justify-end gap-3 pt-4 border-t border-slate-200 dark:border-slate-700">
                    <Button label="Cancel" text @click="closeModal"
                        class="text-slate-600! dark:text-slate-400! hover:bg-slate-100! dark:hover:bg-slate-700! rounded-xl!" />
                    <Button label="Delete Account" icon="pi pi-trash" severity="danger" :loading="form.processing"
                        @click="deleteUser" class="bg-red-500! hover:bg-red-600! border-red-500! rounded-xl!" />
                </div>
            </template>
        </Dialog>
    </section>
</template>
