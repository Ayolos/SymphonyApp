<script setup>
import {ref} from 'vue';
import {useForm} from '@inertiajs/vue3';
import ActionSection from '@/Components/ActionSection.vue';
import DangerButton from '@/Components/DangerButton.vue';
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;

    setTimeout(() => passwordInput.value.focus(), 250);
};

const deleteUser = () => {
    form.delete(route('current-user.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;

    form.reset();
};
</script>

<template>
    <ActionSection>
        <template #title>
            Supprimer le compte
        </template>

        <template #description>
            Supprimer définitivement votre compte.
        </template>

        <template #content>
            <div class="max-w-xl text-sm text-gray-600">
                Une fois votre compte supprimé, toutes ses ressources et données seront définitivement supprimées. Avant
                de supprimer votre compte, veuillez télécharger
            </div>

            <div class="mt-5">
                <DangerButton @click="confirmUserDeletion">
                    Supprimer le compte
                </DangerButton>
            </div>

            <!-- Delete Account Confirmation Modal -->
            <DialogModal :show="confirmingUserDeletion" @close="closeModal">
                <template #title>
                    Supprimer le compte
                </template>

                <template #content>
                    Etes vous sûr de vouloir supprimer votre compte? Une fois votre compte supprimé, toutes ses
                    ressources et données seront définitivement supprimées. Veuillez entrer votre mot de passe pour
                    confirmer que vous souhaitez supprimer définitivement votre compte.
                    <div class="mt-4">
                        <TextInput
                            ref="passwordInput"
                            v-model="form.password"
                            autocomplete="current-password"
                            class="mt-1 block w-3/4"
                            placeholder="Password"
                            type="password"
                            @keyup.enter="deleteUser"
                        />

                        <InputError :message="form.errors.password" class="mt-2"/>
                    </div>
                </template>

                <template #footer>
                    <SecondaryButton @click="closeModal">
                        Annuler
                    </SecondaryButton>

                    <DangerButton
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        class="ms-3"
                        @click="deleteUser"
                    >
                        Supprimer le compte
                    </DangerButton>
                </template>
            </DialogModal>
        </template>
    </ActionSection>
</template>
