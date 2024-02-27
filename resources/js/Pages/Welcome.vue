<template>
    <Head title="Welcome" />
    <SymphonyLayout :isLogin="canLogin">
        <div v-for="post in posts" :key="post.id" class="flex flex-col p-5">
            <!-- Contenu du post -->
            <div class="flex flex-row items-center pb-4 gap-4">
                <!-- Informations sur l'utilisateur -->
                <img :src="post.user.profile_photo_url" class="w-12 h-12 rounded">
                <div class="flex-col flex">
                    <h1 class="text-gray-400 truncate text-nowrap">{{ post.user.name }}</h1>
                    <span class="text-gray-500 text-sm">@{{ post.user.email }}</span>
                </div>
            </div>
            <div>
                <p>{{ post.content }}</p>
            </div>
            <!-- Afficher les commentaires -->
            <h1>Commentaire</h1>
            <div v-for="comment in post.comments" :key="comment.id">
                <p class="text-gray-500">{{ comment.content }}</p>
            </div>
            <!-- Bouton pour ouvrir le modal -->
            <div class="flex-row flex gap-4">
                <Icon icon="akar-icons:heart" class="text-red-500 w-6 h-6" />
                <button @click="openModal(post)" class="">
                    <Icon icon="akar-icons:comment" class="text-gray-500 w-6 h-6" />
                </button>
            </div>
        </div>

        <!-- Modal -->
        <Modal v-if="isModalOpen" @close="closeModal" :post="selectedPost">
            <template #content>
                <form @submit.prevent="submitComment">
                    <div class="my-5">
                        <textarea v-model="commentForm.content" class="w-full rounded-lg"></textarea>
                    </div>
                    <div class="flex justify-end">
                        <button class="bg-secondary-500 text-white rounded-lg px-4 py-2">Envoyer</button>
                    </div>
                </form>
            </template>
        </Modal>
    </SymphonyLayout>
</template>

<script setup>
import {Head, useForm} from '@inertiajs/vue3';
import SymphonyLayout from "@/Layouts/SymphonyLayout.vue";
import {onMounted, reactive, ref} from "vue";
import { Icon } from "@iconify/vue";
import Modal from "@/Components/Symphony/Modal.vue";

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
    posts: Array,
});

const commentForm = useForm({
    content: "",
    post_id: null,
});

const isModalOpen = ref(false);
const selectedPost = ref(null);

const openModal = (post) => {
    selectedPost.value = post;
    isModalOpen.value = true;
    commentForm.post_id = post.id;
};

const closeModal = () => {
    isModalOpen.value = false;
};

const submitComment = () => {
    console.log(commentForm);
    console.log(commentForm.post_id);
    commentForm.post(route('comments.store'), {
        onSuccess: () => {
            commentForm.reset('content');
        }
    });
};

</script>

<style>
/* Styles pour votre modal */
</style>
