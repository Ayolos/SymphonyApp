<template>
    <Head title="Welcome" />
    <SymphonyLayout :isLogin="canLogin">
        <template #postForm>
            <form @submit.prevent="submitPost" class="w-full">
                <div class="bg-symph-100 rounded-lg">
                    <div class="flex flex-row justify-between items-start h-48">
                        <div class="flex-col items-center gap-4 p-4 w-full h-full">
                            <div class="flex flex-row items-center gap-4 p-4">
                                <img :src="$page.props.auth.user.profile_photo_url" class="w-12 h-12 rounded">
                                <div class="flex-col flex">
                                    <h1 class="text-gray-400 truncate text-nowrap">{{ $page.props.auth.user.name }}</h1>
                                    <span class="text-gray-500 text-sm">@{{ $page.props.auth.user.username }}</span>
                                </div>
                            </div>
                            <div class="pl-4 pr-20 flex flex-row justify-between items-end gap-8">
                                <!-- Définir la taille du textarea -->
                                <textarea v-model="formPost.content" class="w-full h-20 focus:ring-0 resize-none border-0 p-0 rounded-lg bg-symph-100" placeholder="Exprimez-vous..."></textarea>
                                <div>
                                    <button class="bg-symph-500 text-white rounded-lg px-4 py-2">Publier</button>
                                </div>
                            </div>
                        </div>
                        <img src="https://fakeimg.pl/300/" class="h-48 w-max object-cover rounded-e-lg">
                    </div>
                </div>
            </form>
        </template>
        <div class="p-10" v-for="post in posts" :key="post.id" >
            <a :href="route('posts.show', { id: post.id })" class="flex flex-col">
                <!-- Contenu du post -->
                <PostInfo :src="post.user.profile_photo_url">
                    <template #name>
                        {{ post.user.name }}
                    </template>
                    <template #at>
                        {{ post.user.username }}
                    </template>
                </PostInfo>
                <div>
                    <p>{{ post.content }}</p>
                </div>
                <!-- Afficher les commentaires -->
                <h1>Commentaire</h1>
                <div v-for="comment in post.comments" :key="comment.id">
                    <p class="text-gray-500">{{ comment.content }}</p>
                </div>
            </a>
            <!-- Bouton pour ouvrir le modal -->
            <div class="flex-row flex gap-4">
                <Link as="button" method="post" :href="post.linkedByUser ? route('posts.unlike', { post: post.id }) : route('posts.like', { post: post.id })" :class="[ post.linkedByUser ? 'bg-secondary-500' : '']" preserve-scroll>
                    <Icon icon="akar-icons:heart" class="w-6 h-6" />
                </Link>
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
import {Head, router, useForm, Link} from '@inertiajs/vue3';
import SymphonyLayout from "@/Layouts/SymphonyLayout.vue";
import {computed, onMounted, reactive, ref} from "vue";
import { Icon } from "@iconify/vue";
import Modal from "@/Components/Symphony/Modal.vue";
import PostInfo from "@/Components/Symphony/PostInfo.vue";

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
    posts: Array,
});

const like = ref(false);

const formPost = useForm({
    content: '',
});

const formComment = useForm({
    content: "",
    post_id: null,
});

const isModalOpen = ref(false);
const selectedPost = ref(null);

const openModal = (post) => {
    selectedPost.value = post;
    isModalOpen.value = true;
    formComment.post_id = post.id;
};

const closeModal = () => {
    isModalOpen.value = false;
};

const submitPost = () => {
    formPost.post(route('posts.store'), {
        onSuccess: () => {
            formPost.reset('content');
        }
    });
};

const submitComment = () => {
    closeModal();
    formComment.post(route('comments.store'), {
        preserveScroll: true,
        onSuccess: () => {
            formComment.reset('content');
        }
    });
};

const toogleLike = (post) => {
    if (post.linkedByUser) {
        router.post(route('posts.unlike', { post: post.id }), {
            preserveState: true,
            preserveScroll: true,
        });
    } else {
        router.post(route('posts.like', { post: post.id }), {
            preserveState: true,
            preserveScroll: true,
        });
    }

};


</script>


<style>
/* Styles pour votre modal */
</style>
