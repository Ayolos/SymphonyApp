<template>
    <Head title="Feed" />
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
                                <textarea required v-model="formPost.content" class="w-full h-20 focus:ring-0 resize-none border-0 p-0 rounded-lg bg-symph-100" placeholder="Exprimez-vous..."></textarea>
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
        <div v-for="post in posts" :key="post.id" class="pb-10">
            <Post :href="route('posts.show', {id: post.id})"
                  :src="post.user.profile_photo_url">
                <template #name>
                    {{ post.user.name }}
                </template>
                <template #at>
                    @{{ post.user.username }}
                </template>
                <template #content>
                    {{ post.content }}
                </template>
                <template #likeButton>
                    <div class="flex flex-row gap-2 items-center">
                        <Link as="button" method="post" :href="post.linkedByUser ? route('posts.unlike', { post: post.id }) : route('posts.like', { post: post.id })" >
                            <Icon icon="iconamoon:heart-duotone" class="w-6 h-6" :class="[ post.linkedByUser ? 'text-secondary-500' : 'text-gray-300']" />
                        </Link>
                        <h1 class="text-md text-symph-200 font-bold">{{ post.nbLikes }}</h1>
                    </div>
                    <div class="flex flex-row gap-2 items-center">
                        <button @click="openModal(post)" class="text-gray-300">
                            <Icon icon="iconamoon:comment-duotone" class="w-5 h-5" />
                        </button>
                        <h1 class="text-md text-symph-200 font-bold">{{ post.nbComments }}</h1>
                    </div>
                </template>
            </Post>
            <div v-for="(comment, index) in post.comments" :key="comment.id" class="px-10 flex w-full items-center relative">
                <div class="flex-shrink-0 mr-4">
                    <div class="h-full w-0.5 bg-symph-300 absolute top-0 left-11"></div> <!-- Vertical Line -->
                    <div class="h-2.5 w-2.5 bg-symph-300 rounded-full"></div> <!-- Circle -->
                </div>
                <div class="bg-symph-100 rounded-lg my-3 w-full">
                    <Post :src="comment.user.profile_photo_url">
                        <template #name>
                            {{ comment.user.name }}
                        </template>
                        <template #at>
                            @{{ comment.user.username }}
                        </template>
                        <template #content>
                            {{ comment.content }}
                        </template>
                    </Post>
                </div> <!-- Comment Content -->
            </div>
        </div>

        <!-- Modal -->
        <Modal v-if="isModalOpen" @close="closeModal">
            <template #content>
                <form @submit.prevent="submitComment">
                    <div class="my-5">
                        <textarea required v-model="formComment.content" class="w-full rounded-lg"></textarea>
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
import Modal from "@/Components/Symphony/Modal/Modal.vue";
import PostInfo from "@/Components/Symphony/PostInfo.vue";
import Post from "@/Components/Symphony/Post/Post.vue";

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


</script>


<style>
/* Styles pour votre modal */
</style>
