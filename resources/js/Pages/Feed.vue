<template>
    <Head title="Feed" />
    <SymphonyLayout :isLogin="canLogin">
        <template #trendingUsers>
            <h1 class="text-white font-bold text-xl pb-5">Vous aimeriez aussi...</h1>
            <div v-for="trendingUser in trendingUsers" class="flex flex-row gap-4 justify-between">
                <div class="flex flex-row items-center pb-4 gap-4">
                    <img :src=trendingUsers.profile_photo_url class="w-12 h-12 rounded">
                    <div class="flex-col flex">
                        <span class="text-gray-400 truncate text-nowrap">{{trendingUser.name}}</span>
                        <span class="text-gray-500 text-sm">@{{trendingUser.username}}</span>
                    </div>
                </div>
                <div class="flex flex-row items-center gap-4 pr-4">
                    <form @submit.prevent="follow(trendingUser)">
                        <button class="bg-symph-500 text-white rounded-lg px-4 py-2">Suivre</button>
                    </form>
                </div>
            </div>
        </template>
        <template #postForm>
            <PostForm></PostForm>
        </template>
        <div v-for="post in posts" :key="post.id" class="pb-5">
            <Post
                  :createdAt="post.created_at"
                  :user-id="post.user.id"
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
                <template #media>
                  <PlayerAudio :song="post.song"></PlayerAudio>
                </template>
                <template #likeButton>
                    <Link :href="route('posts.show', {id: post.id})">
                      <div class="flex flex-row gap-2 items-center text-gray-300 hover:text-secondary">
                        <Icon icon="icon-park-twotone:more-three" class="w-6 h-6" />
                        <h1 class="text-md font-bold">voir plus</h1>
                      </div>
                    </Link>
                    <div class="flex flex-row gap-2 items-center">
                        <Link as="button" method="post" :href="post.isLiked ? route('posts.unlike', { post: post.id }) : route('posts.like', { post: post.id })" >
                            <Icon icon="iconamoon:heart-duotone" class="w-6 h-6 transition hover:scale-110 hover:rotate-6 ease-in-out" :class="[ post.isLiked ? 'text-secondary-500' : 'text-gray-300']" />
                        </Link>
                        <h1 class="text-md text-symph-200 font-bold">{{ post.nbLikes }}</h1>
                    </div>
                    <div class="flex flex-row gap-2 items-center">
                        <MainModal @submit="submitComment(post.id)">
                            <template #button>
                                <Icon icon="iconamoon:comment-duotone" class="w-5 h-5 transition hover:scale-110 ease-in-out text-gray-300" />
                            </template>
                            <template #modalTitle>
                                Ajouter un commentaire
                            </template>
                            <template #content>
                                <div class="flex flex-col gap-3 px-4 justify-end">
                                    <textarea required v-model="formComment.content" placeholder="Ecrit ton commentaire" class="w-full text-symph-200 h-48 rounded-lg bg-symph-800 border-symph-500 resize-none"></textarea>
                                    <button class="bg-secondary-500 text-white rounded-lg px-4 py-2">Envoyer</button>
                                </div>
                            </template>
                        </MainModal>
                        <h1 class="text-md text-symph-200 font-bold">{{ post.nbComments }}</h1>
                    </div>
                    <!-- Share button -->
                    <div class="flex flex-row gap-2 items-center">
                      <button @click="copy(route('posts.show', {id: post.id}))" class="text-gray-300">
                        <div v-if="copied"  class="absolute shadow top-20 right-2 flex items-center p-4 mb-4 text-sm text-symph-100 border border-symph-400 rounded-lg bg-symph-600" role="alert">
                          <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                          </svg>
                          <span class="sr-only">Info</span>
                          <div>
                            <span class="font-medium">Eh oh !</span> Le lien est copié dans le presse-papier
                          </div>
                        </div>
                        <Icon icon="solar:share-line-duotone" class="w-6 h-6" />
                      </button>
                    </div>
                </template>
            </Post>

            <!--<div v-for="(comment, index) in post.comments" :key="comment.id" class="pl-10 pt-3 flex flex-col w-full items-center relative">
                <div class="bg-symph-100 rounded-lg w-full">
                    <div v-if="index !== post.comments.length - 1" class="absolute w-0.5 h-full top-0 bg-symph-400 left-5"></div>
                    <Post :src="comment.user.profile_photo_url">
                        <template #connectLine>
                            <div class="absolute border-b-2 border-l-2 border-symph-300 rounded-bl-xl h-full w-5 left-5"></div>
                        </template>
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
                                <MainModal @submit="submitCommentReply(comment.id, post.id)">
                                    <template #button>
                                        <Icon icon="iconamoon:comment-duotone" class="w-5 h-5 text-gray-300" />
                                    </template>
                                    <template #modalTitle>
                                        Ajouter un commentaire
                                    </template>
                                    <template #content>
                                        <div class="flex flex-col gap-3 px-4 justify-end">
                                            <textarea required v-model="formReply.content" class="w-full rounded-lg"></textarea>
                                            <button class="bg-secondary-500 text-white rounded-lg px-4 py-2">Envoyer</button>
                                        </div>
                                    </template>
                                </MainModal>
                                <h1 class="text-md text-symph-200 font-bold">{{ comment.nbReplies }}</h1>
                            </div>
                        </template>
                    </Post>
                </div>
                <div v-for="(reply, index) in comment.reply" :key="reply.id" class="pl-10 flex flex-col w-full items-center relative">
                    <div class="bg-symph-100 rounded-lg my-3 w-full">
                        <div v-if="index !== comment.reply.length - 1" class="absolute w-0.5 h-full top-0 bg-symph-400 left-5"></div>
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
                                    <MainModal @submit="submitCommentReply(comment.id, post.id)">
                                        <template #button>
                                            <Icon icon="iconamoon:comment-duotone" class="w-5 h-5 text-gray-300" />
                                        </template>
                                        <template #modalTitle>
                                            Ajouter un commentaire
                                        </template>
                                        <template #content>
                                            <div class="flex flex-col gap-3 px-4 justify-end">
                                                <textarea required v-model="formReply.content" class="w-full rounded-lg"></textarea>
                                                <button class="bg-secondary-500 text-white rounded-lg px-4 py-2">Envoyer</button>
                                            </div>
                                        </template>
                                    </MainModal>
                                </div>
                            </template>
                        </Post>
                    </div>
                </div>
            </div>-->
        </div>
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
import MainModal from "@/Components/Symphony/Modal/MainModal.vue";
import InputLabel from "@/Components/Symphony/Input/InputLabel.vue";
import PostForm from "@/Components/Symphony/Post/PostForm.vue";
import PlayerAudio from "@/Components/Symphony/PlayerAudio.vue";
import { useClipboard } from '@vueuse/core'

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
    posts: Array,
    trendingUsers: Array,
});

const source = ref('')
const { text, copy, copied, isSupported } = useClipboard({ source })

const like = ref(false);

const formComment = useForm({
    content: "",
    post_id: null,
});

const formReply = useForm({
    content: "",
    parent_id: null,
    post_id: null,
});
const submitCommentReply = (commentId, postId) => {
    formReply.parent_id = commentId;
    formReply.post_id = postId;
    formReply.post(route('comments.reply'), {
        preserveScroll: true,
        onSuccess: () => {
            formReply.reset('content');
        }
    });
};
const submitComment = (postId) => {
    formComment.post_id = postId;
    formComment.post(route('comments.store'), {
        preserveScroll: true,
        onSuccess: () => {
            formComment.reset('content');
        }
    });
};

const userTrendingFormPost = useForm({
    trendingUser: ''
})

const follow = (user) => {
  userTrendingFormPost.trendingUser = user.id;
  userTrendingFormPost.post(route('user.follow'));
}


</script>


<style>
/* Styles pour votre modal */
.progress-bar {
  @apply w-full h-2 bg-gray-300;
}

.progress {
  @apply h-full bg-green-500;
}
</style>
