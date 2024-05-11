
<template>
    <Head title="Feed" />
    <SymphonyLayout :isLogin="canLogin">
        <template #trendingUsers>
            <div>
                <!-- Afficher les utilisateurs tendance -->
                <div v-for="trendingUser in trendingUsers" :key="trendingUser.id" class="flex flex-row gap-4 justify-between">
                    <div class="flex flex-row items-center pb-4 gap-4">
                        <img :src="trendingUser.profile_photo_url" class="w-12 h-12 rounded">
                        <div class="flex-col flex">
                            <span class="text-gray-400">{{ trendingUser.name }}</span>
                            <span class="text-gray-500 text-sm">@{{ trendingUser.username }}</span>
                        </div>
                    </div>
                    <div class="flex flex-row items-center gap-4">
                        <!-- Bouton qui change en fonction de l'état de suivi -->
                        <form @submit.prevent="trendingUser.isFollowed ? toggleUnFollow(trendingUser) : toggleFollowing(trendingUser)">
                            <button class="bg-symph-500 text-white rounded p-1 aspect-square">
                              <Icon v-if="trendingUser.isFollowed" icon="material-symbols:check-indeterminate-small-rounded" class="w-6 h-6" />
                              <Icon v-else icon="jam:plus" class="w-6 h-6" />
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </template>
        <template #postForm>
            <PostForm></PostForm>
        </template>
        <div v-for="post in posts" :key="post.id">
            <!--:href="route('posts.show', {id: post.id})"-->
            <Post
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
                                <div class="flex flex-col gap-2 px-8">
                                    <div class="flex flex-col gap-2">
                                        <UserInfo :created_at="post.created_at" :name="post.user.name" :content="post.content" :username="post.user.username" :profile_src="post.user.profile_photo_url" />
                                    </div>
                                    <div class="flex flex-row items-start gap-4 mt-8">
                                        <img :src="$page.props.auth.user.profile_photo_url" class="w-12 h-12 rounded">
                                        <div class="w-full">
                                            <textarea required v-model="formComment.content" maxlength="255" placeholder="Ecrit ton commentaire" class="w-full text-symph-200 h-48 rounded-lg bg-symph-800 border-symph-500 resize-none"></textarea>
                                            <CounterMessage class="text-symph-100 w-full text-end" :message="formComment.content" :max-characters="255" />
                                        </div>
                                    </div>
                                    <button class="bg-secondary-500 text-white rounded-lg px-4 py-2 mt-3">Envoyer</button>
                                </div>
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
import {useClipboard} from "@vueuse/core";
import CounterMessage from "@/Components/Symphony/CounterMessage.vue";
import Alerts from "@/Components/Symphony/Alerts.vue";
import ShareButton from "@/Components/Symphony/Button/ShareButton.vue";
import UserInfo from "@/Components/Symphony/UserInfo.vue";

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
    posts: Array,
    trendingUsers: Array,
    follower: Number,
    following: Number,
});

const like = ref(false);

const formComment = useForm({
    content: "",
    post_id: null,
});

const formFollow = useForm({
  following_id: null,
});

const source = ref('')
const { text, copy, copied, isSupported } = useClipboard({ source })

const submitComment = (postId) => {
    formComment.post_id = postId;
    formComment.post(route('comments.store'), {
        preserveScroll: true,
        onSuccess: () => {
            formComment.reset('content');
        },
        onError: (e) => {
            console.log(e)
        }
    });
};

// Fonction pour suivre/désabonner avec formulaire
const toggleFollowing = async (trendingUser) => {
  formFollow.following_id = trendingUser.id;
  formFollow.post(route('user.follow'), {
    preserveScroll: true,
    onSuccess: () => {
    }
  })
};

const toggleUnFollow = async (trendingUser) => {
  formFollow.following_id = trendingUser.id;
  formFollow.delete(route('user.unfollow', {user: trendingUser.id}), {
    preserveScroll: true,
    onSuccess: () => {
    }
  })
};

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
