<script setup>
import SymphonyLayout from "@/Layouts/SymphonyLayout.vue";
import PostInfo from "@/Components/Symphony/PostInfo.vue";
import {Link, useForm} from "@inertiajs/vue3";
import Post from "@/Components/Symphony/Post/Post.vue";
import {Icon} from "@iconify/vue";
import {ref} from "vue";
import Modal from "@/Components/Symphony/Modal/Modal.vue";
import PlayerAudio from "@/Components/Symphony/PlayerAudio.vue";

defineProps({
    post: Object,
});

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

const formReply = useForm({
    content: "",
    parent_id: null,
    post_id: null,
});

const selectedComment = ref(null);
const isReplyModalOpen = ref(false);
const isCommentModalOpen = ref(false);


const openModal = (post) => {
    selectedPost.value = post;
    isModalOpen.value = true;
    formComment.post_id = post.id;
};

const openCommentModal = (comment, post) => {
    selectedComment.value = comment;
    isCommentModalOpen.value = true;
    formComment.parent_id = comment.id;
    formComment.post_id = post.id;
};

const openReplyModal = (comment, post) => {
    selectedComment.value = comment;
    isReplyModalOpen.value = true;
    formReply.content = '@' + comment.user.username + ' '
    formReply.parent_id = comment.id;
    formReply.post_id = post.id;
};
const closeModal = () => {
    isModalOpen.value = false;
};

const closeModalReply = () => {
    isReplyModalOpen.value = false;
};

const closeModalComment = () => {
    isCommentModalOpen.value = false;
};

const submitPost = () => {
    formPost.post(route('posts.store'), {
        onSuccess: () => {
            formPost.reset('content');
        }
    });
};

const submitReply = () => {
    closeModalReply();
    formReply.post(route('comments.reply'), {
        preserveScroll: true,
        onSuccess: () => {
            formReply.reset('content');
        }
    });
};
const submitComment = () => {
    closeModalComment();
    formComment.post(route('comments.reply'), {
        preserveScroll: true,
        onSuccess: () => {
            formComment.reset('content');
        }
    });
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
        <div class="">
            <Post :src="post.user.profile_photo_url" :connectLine="false">
                <template #name>
                    {{ post.user.name }}
                </template>
                <template #at>
                    @{{ post.user.username }}
                </template>
                <template #content>
                    {{ post.content }}
                </template>
              <template #media>
                <PlayerAudio :song="post.song"></PlayerAudio>
              </template>
                <template #likeButton>
                    <div class="flex flex-row gap-2 items-center">
                        <Link as="button" method="post" :href="post.isLiked ? route('posts.unlike', { post: post.id }) : route('posts.like', { post: post.id })" >
                            <Icon icon="iconamoon:heart-duotone" class="w-6 h-6" :class="[ post.isLiked ? 'text-secondary-500' : 'text-gray-300']" />
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
            <div v-for="(comment, index) in post.comments" :key="comment.id" class="pl-10 mt-3 flex flex-col w-full items-center relative">
              <div v-if="index !== post.comments.length - 1" class="absolute w-0.5 h-full top-0 bg-symph-400 left-5"></div>
              <div class="bg-symph-100 rounded-lg w-full">
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
                        <template #likeButton>
                            <div class="flex flex-row gap-2 items-center">
                                <Link as="button" method="post" :href="comment.isLiked ? route('comments.unlike', { comment: comment.id }) : route('comments.like', { comment: comment.id })" >
                                    <Icon icon="iconamoon:heart-duotone" class="w-6 h-6" :class="[ comment.isLiked ? 'text-secondary-500' : 'text-gray-300']" />
                                </Link>
                                <h1 class="text-md text-symph-200 font-bold">{{ comment.nbLikes }}</h1>
                            </div>
                            <div class="flex flex-row gap-2 items-center">
                                <button @click="openCommentModal(comment, post)" class="text-gray-300">
                                    <Icon icon="iconamoon:comment-duotone" class="w-5 h-5" />
                                </button>
                                <h1 class="text-md text-symph-200 font-bold">{{ comment.nbReplies }}</h1>
                            </div>
                        </template>
                    </Post>
                </div> <!-- Comment Content -->
                <div v-for="(reply, index) in comment.reply" :key="reply.id" class="pl-10 flex w-full items-center relative">
                  <div v-if="index !== comment.reply.length - 1" class="absolute w-0.5 h-full top-0 bg-symph-400 left-5"></div>
                  <div class="bg-symph-100 rounded-lg my-3 w-full">
                        <Post :src="reply.user.profile_photo_url">
                            <template #name>
                                {{ reply.user.name }}
                            </template>
                            <template #at>
                                @{{ reply.user.username }}
                            </template>
                            <template #content>
                                {{ reply.content }}
                            </template>
                            <template #likeButton>
                                <div class="flex flex-row gap-2 items-center">
                                    <Link as="button" method="post" :href="reply.isLiked ? route('comments.unlike', { comment: reply.id }) : route('comments.like', { comment: reply.id })" >
                                        <Icon icon="iconamoon:heart-duotone" class="w-6 h-6" :class="[ reply.isLiked ? 'text-secondary-500' : 'text-gray-300']" />
                                    </Link>
                                    <h1 class="text-md text-symph-200 font-bold">{{ reply.nbLikes }}</h1>
                                </div>
                                <div class="flex flex-row gap-2 items-center">
                                    <button @click="openReplyModal(comment, post)" class="text-gray-300">
                                        <Icon icon="iconamoon:comment-duotone" class="w-5 h-5" />
                                    </button>
                                </div>
                            </template>
                        </Post>
                    </div> <!-- Comment Content -->
                </div>
            </div>
        </div>

        <Modal v-if="isReplyModalOpen" @close="closeModalReply">
            <template #content>
                <form @submit.prevent="submitReply">
                    <h1>envoyer un reply</h1>
                    <div class="my-5">
                        <textarea required v-model="formReply.content" class="w-full rounded-lg"></textarea>
                    </div>
                    <div class="flex justify-end">
                        <button class="bg-secondary-500 text-white rounded-lg px-4 py-2">Envoyer</button>
                    </div>
                </form>
            </template>
        </Modal>

        <Modal v-if="isCommentModalOpen" @close="closeModalComment">
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
