<script setup>
import SymphonyLayout from "@/Layouts/SymphonyLayout.vue";
import PostInfo from "@/Components/Symphony/PostInfo.vue";
import {Link, useForm} from "@inertiajs/vue3";
import Post from "@/Components/Symphony/Post/Post.vue";
import {Icon} from "@iconify/vue";
import {ref} from "vue";
import Modal from "@/Components/Symphony/Modal/Modal.vue";

defineProps({
    post: Object,
});

const submitComment = () => {
    closeCommentModal();
    formComment.post(route('comments.store'), {
        onSuccess: () => {
            formComment.reset('content');
        }
    });
};

const like = ref(false);
const postLink = ref(window.location.href);

const formPost = useForm({
    content: '',
});

const formComment = useForm({
    content: "",
    post_id: null,
});

const isModalOpen = ref(false);
const isShareModalOpen = ref(false);
const selectedPost = ref(null);

const openShareModal = () => {
    isShareModalOpen.value = true;
};

const closeShareModal = () => {
    isShareModalOpen.value = false;
};

const openCommentModal = (post) => {
    selectedPost.value = post;
    isModalOpen.value = true;
    formComment.post_id = post.id;
};

const closeCommentModal = () => {
    isModalOpen.value = false;
};

const copyLink = () => {
    navigator.clipboard.writeText(postLink.value).then(() => {
        alert('Link copied to clipboard')
    }).catch((error) => {
        console.error('Error copying text: ', error);
    });
}

</script>

<template>
    <SymphonyLayout>
        <div class="p-10">
            <Post :src="post.user.profile_photo_url">
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
                        <button @click="openCommentModal(post)" class="text-gray-300">
                            <Icon icon="iconamoon:comment-duotone" class="w-5 h-5" />
                        </button>
                        <h1 class="text-md text-symph-200 font-bold">{{ post.nbComments }}</h1>
                    </div>
                    <div class="flex flex-row gap-2 items-center">
                        <button @click="openShareModal(post)" class="text-gray-300">
                            <Icon icon="solar:share-line-duotone" class="w-6 h-6" />
                        </button>
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

        <Modal v-if="isShareModalOpen" @close="closeShareModal">
            <template #content>
                <button class="bg-secondary-500 p-3 rounded-lg" @click="copyLink">Copy Link</button>
            </template>
        </Modal>

        <Modal v-if="isModalOpen" @close="closeCommentModal">
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
