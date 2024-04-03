<script setup>

import SymphonyLayout from "@/Layouts/SymphonyLayout.vue";
import Logo from "/public/Logo.png";
import {Link, useForm} from "@inertiajs/vue3";
import {Icon} from "@iconify/vue";
import Modal from "@/Components/Symphony/Modal/Modal.vue";
import Post from "@/Components/Symphony/Post/Post.vue";
import {onMounted, ref} from "vue";

defineProps({
    posts: Object,
    nbPosts: Number,
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

const submitComment = () => {
    closeModal();
    formComment.post(route('comments.store'), {
        preserveScroll: true,
        onSuccess: () => {
            formComment.reset('content');
        }
    });
};

const closeModal = () => {
    isModalOpen.value = false;
};

</script>

<template>
    <SymphonyLayout>
        <template #postForm>
            <div class="w-full flex flex-row bg-symph-900 rounded-lg h-[20vh]">
                <img :src="Logo" class="h-full aspect-square p-3">
                <div class="w-full flex flex-col items-center text-gray-500">
                    <h1 class="text-gray-500">Profile</h1>
                    <p>{{ $page.props.auth.user.name }}</p>
                    <p>@{{ $page.props.auth.user.username }}</p>
                    <p>nbPost {{ nbPosts }}</p>
                </div>
            </div>

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
                        <Link as="button" method="post" :href="post.isLiked ? route('posts.unlike', { post: post.id }) : route('posts.like', { post: post.id })" preserve-scroll>
                            <Icon icon="iconamoon:heart-duotone" class="w-6 h-6" :class="[ post.isLiked ? 'text-secondary-500' : 'text-gray-300']" />
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

<style scoped>

</style>
