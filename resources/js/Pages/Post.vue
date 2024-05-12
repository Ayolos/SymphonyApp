<script setup>
import SymphonyLayout from "@/Layouts/SymphonyLayout.vue";
import {Link, useForm} from "@inertiajs/vue3";
import Post from "@/Components/Symphony/Post/Post.vue";
import {Icon} from "@iconify/vue";
import {ref} from "vue";
import PlayerAudio from "@/Components/Symphony/PlayerAudio.vue";
import MainModal from "@/Components/Symphony/Modal/MainModal.vue";
import {useClipboard} from '@vueuse/core'
import ShareButton from "@/Components/Symphony/Button/ShareButton.vue";
import CounterMessage from "@/Components/Symphony/CounterMessage.vue";
import UserInfo from "@/Components/Symphony/Post/UserInfo.vue";
import UserCommentInfo from "@/Components/Symphony/UserCommentInfo.vue";
import Tooltip from "@/Components/Symphony/Tooltip.vue";

defineProps({
    post: Object,
    trendingUsers: Array,
});

const like = ref(false);

const source = ref('')
const {text, copy, copied, isSupported} = useClipboard({source})


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
</script>

<template>
    <SymphonyLayout>
        <template #trendingUsers>
            <div v-for="trendingUser in trendingUsers" :key="trendingUser.id" class="flex w-full flex-row mb-4 items-center gap-4 justify-between">
                <div class="flex flex-row items-center gap-4 w-3/4">
                    <img :src="trendingUser.profile_photo_url" class="w-12 h-12 rounded">
                    <div class="flex-col flex">
                        <Tooltip>
                            <template #button>
                                <UserInfo class="overflow-hidden w-32" :name="trendingUser.name" :username="trendingUser.username" />
                            </template>
                            <template #content>
                                <UserInfo :name="trendingUser.name" :username="trendingUser.username" />
                            </template>
                        </Tooltip>
                    </div>
                </div>
                <div class="flex flex-row items-center gap-4">
                    <!-- Bouton qui change en fonction de l'état de suivi -->
                    <form @submit.prevent="trendingUser.isFollowed ? toggleUnFollow(trendingUser) : toggleFollowing(trendingUser)">
                        <button class="bg-symph-500 text-white rounded-full p-1 aspect-square">
                            <Icon v-if="trendingUser.isFollowed" icon="material-symbols:check-indeterminate-small-rounded" class="w-6 h-6" />
                            <Icon v-else icon="jam:plus" class="w-6 h-6" />
                        </button>
                    </form>
                </div>
            </div>
        </template>
        <div class="">
            <Post
                class="bg-symph-700"
                :post="post"
                :connectLine="false"
                :createdAt="post.created_at"
                :user-id="post.user.id"
                :src="post.user.profile_photo_url">
                <template #likeButton>
                    <Link :href="route('posts.show', {id: post.id})">
                        <div class="flex flex-row gap-2 items-center text-gray-300 hover:text-secondary">
                            <Icon icon="icon-park-twotone:more-three" class="w-5 h-5" />
                            <h1 class="text-md font-bold w-max">voir plus</h1>
                        </div>
                    </Link>
                    <div class="flex flex-row gap-2 items-center">
                        <Link as="button" method="post" :href="post.isLiked ? route('posts.unlike', { post: post }) : route('posts.like', { post: post })" >
                            <Icon icon="uil:heart" class="w-5 h-5 transition hover:scale-110 hover:rotate-6 ease-in-out" :class="[ post.isLiked ? 'text-secondary-500' : 'text-gray-300']" />
                        </Link>
                        <h1 class="text-md text-symph-200 font-bold">{{ post.nbLikes }}</h1>
                    </div>
                    <div class="flex flex-row gap-2 items-center">
                        <MainModal @submit="submitComment(post.id)">
                            <template #button>
                                <Icon icon="uil:comment" class="w-5 h-5 transition hover:scale-110 ease-in-out text-gray-300" />
                            </template>
                            <template #modalTitle>
                                Ajouter un commentaire
                            </template>
                            <template #content>
                                    <div class="flex flex-col gap-2">
                                        <UserCommentInfo :created_at="post.created_at" :name="post.user.name" :content="post.content" :username="post.user.username" :profile_src="post.user.profile_photo_url" />
                                    </div>
                                    <div class="flex flex-row items-start gap-4 mt-8">
                                        <img :src="$page.props.auth.user.profile_photo_url" class="w-12 h-12 rounded">
                                        <div class="w-full">
                                            <textarea required v-model="formComment.content" maxlength="255" placeholder="Ecrit ton commentaire" class="w-full text-symph-200 h-48 rounded-lg bg-symph-800 border-symph-500 resize-none"></textarea>
                                            <CounterMessage class="text-symph-100 w-full text-end" :message="formComment.content" :max-characters="255" />
                                        </div>
                                    </div>
                                <button class="bg-secondary/20 hover:bg-secondary/40 border border-secondary text-white rounded-md px-4 py-2 mt-3">Envoyer</button>
                            </template>
                        </MainModal>
                        <h1 class="text-md text-symph-200 font-bold">{{ post.nbComments }}</h1>
                    </div>
                    <!-- Share button -->
                    <div class="flex flex-row gap-2 items-center">
                        <ShareButton :copy-text="route('posts.show', {id: post.id})"></ShareButton>
                    </div>
                </template>
            </Post>
            <div v-for="(comment, index) in post.comments" :key="comment.id" class="flex border-b border-symph-500 flex-col w-full items-center relative">
                <div class="w-full">
                    <Post
                        :line="true"
                        :border="false"
                        :post="comment"
                        :connectLine="false"
                        :createdAt="comment.created_at"
                        :user-id="comment.user.id"
                        :src="comment.user.profile_photo_url">
                        <template #likeButton>
                            <div class="flex flex-row gap-2 items-center">
                                <Link as="button" method="post" :href="comment.isLiked ? route('comments.unlike', { comment: comment }) : route('comments.like', { comment: comment })" >
                                    <Icon icon="uil:heart" class="w-5 h-5 transition hover:scale-110 hover:rotate-6 ease-in-out" :class="[ comment.isLiked ? 'text-secondary-500' : 'text-gray-300']" />
                                </Link>
                                <h1 class="text-md text-symph-200 font-bold">{{ comment.nbLikes }}</h1>
                            </div>
                            <div class="flex flex-row gap-2 items-center">
                                <MainModal @submit="submitCommentReply(comment.id, post.id)">
                                    <template #button>
                                        <Icon icon="uil:comment" class="w-5 h-5 transition hover:scale-110 ease-in-out text-gray-300" />
                                    </template>
                                    <template #modalTitle>
                                        Ajouter un commentaire
                                    </template>
                                    <template #content>
                                            <div class="flex flex-col gap-2">
                                                <UserCommentInfo :created_at="comment.created_at" :name="comment.user.name" :content="comment.content" :username="comment.user.username" :profile_src="comment.user.profile_photo_url" />
                                            </div>
                                            <div class="flex flex-row items-start gap-4 mt-8">
                                                <img :src="$page.props.auth.user.profile_photo_url" class="w-12 h-12 rounded">
                                                <div class="w-full">
                                                    <textarea required v-model="formReply.content" maxlength="255" placeholder="Ecrit ton commentaire" class="w-full text-symph-200 h-48 rounded-lg bg-symph-800 border-symph-500 resize-none"></textarea>
                                                    <CounterMessage class="text-symph-100 w-full text-end" :message="formReply.content" :max-characters="255" />
                                                </div>
                                            </div>
                                        <button class="bg-secondary/20 hover:bg-secondary/40 border border-secondary text-white rounded-md px-4 py-2 mt-3">Envoyer</button>
                                    </template>
                                </MainModal>
                                <h1 class="text-md text-symph-200 font-bold">{{ comment.nbReplies }}</h1>
                            </div>
                            <!-- Share button -->
                            <div class="flex flex-row gap-2 items-center">

                            </div>
                        </template>
                    </Post>
                </div>
                <div v-for="(reply, index) in comment.reply" :key="reply.id" class="pl-14  flex w-full items-center relative">
                    <div class="w-full">
                        <Post
                            :line="true"
                            :border="false"
                            :post="reply"
                            :connectLine="false"
                            :createdAt="reply.created_at"
                            :user-id="reply.user.id"
                            :src="reply.user.profile_photo_url">
                            <template #likeButton>
                                <div class="flex flex-row gap-2 items-center">
                                    <Link as="button" method="post" :href="reply.isLiked ? route('comments.unlike', { comment: reply }) : route('comments.like', { comment: reply })" >
                                        <Icon icon="uil:heart" class="w-5 h-5 transition hover:scale-110 hover:rotate-6 ease-in-out" :class="[ reply.isLiked ? 'text-secondary-500' : 'text-gray-300']" />
                                    </Link>
                                    <h1 class="text-md text-symph-200 font-bold">{{ reply.nbLikes }}</h1>
                                </div>
                                <div class="flex flex-row gap-2 items-center">
                                    <MainModal @submit="submitCommentReply(comment.id, post.id)">
                                        <template #button>
                                            <Icon icon="uil:comment" class="w-5 h-5 transition hover:scale-110 ease-in-out text-gray-300" />
                                        </template>
                                        <template #modalTitle>
                                            Ajouter un commentaire
                                        </template>
                                        <template #content>
                                                <div class="flex flex-col gap-2">
                                                    <UserCommentInfo :created_at="reply.created_at" :name="reply.user.name" :content="reply.content" :username="reply.user.username" :profile_src="reply.user.profile_photo_url" />
                                                </div>
                                                <div class="flex flex-row items-start gap-4 mt-8">
                                                    <img :src="$page.props.auth.user.profile_photo_url" class="w-12 h-12 rounded">
                                                    <div class="w-full">
                                                        <textarea required v-model="formReply.content" maxlength="255" placeholder="Ecrit ton commentaire" class="w-full text-symph-200 h-48 rounded-lg bg-symph-800 border-symph-500 resize-none"></textarea>
                                                        <CounterMessage class="text-symph-100 w-full text-end" :message="formReply.content" :max-characters="255" />
                                                    </div>
                                                </div>
                                            <button class="bg-secondary/20 hover:bg-secondary/40 border border-secondary text-white rounded-md px-4 py-2 mt-3">Envoyer</button>
                                        </template>
                                    </MainModal>
                                    <h1 class="text-md text-symph-200 font-bold">{{ reply.nbComments }}</h1>
                                </div>
                                <!-- Share button -->
                                <div class="flex flex-row gap-2 items-center">

                                </div>
                            </template>
                        </Post>
                    </div>
                </div>
            </div>
        </div>
    </SymphonyLayout>
</template>
