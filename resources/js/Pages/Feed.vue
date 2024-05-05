
<template>
    <Head title="Feed" />
    <SymphonyLayout :isLogin="canLogin">
        <!--<template #trendingUsers>
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
                    <form @submit.prevent="toggleFollowing(trendingUser)">
                        <button class="bg-symph-500 text-white rounded-lg px-4 py-2">
                            {{ isFollowing(trendingUser.id) ? 'Ne plus Suivre' : 'Suivre' }}
                        </button>
                    </form>
                </div>
            </div>
        </template>-->
        <template #trendingUsers>
            <h1 class="text-white font-bold text-xl pb-5">Vous aimeriez aussi...</h1>
            <div v-if="followingIds.length >= 0">
                <!-- Afficher les utilisateurs tendance -->
                <div v-for="trendingUser in trendingUsers" :key="trendingUser.id" class="flex flex-row gap-4 justify-between">
                    <div class="flex flex-row items-center pb-4 gap-4">
                        <img :src="trendingUser.profile_photo_url" class="w-12 h-12 rounded">
                        <div class="flex-col flex">
                            <span class="text-gray-400">{{ trendingUser.name }}</span>
                            <span class="text-gray-500 text-sm">@{{ trendingUser.username }}</span>
                        </div>
                    </div>
                    <div class="flex flex-row items-center gap-4 pr-4">
                        <!-- Bouton qui change en fonction de l'état de suivi -->
                        <form @submit.prevent="toggleFollowing(trendingUser)">
                            <button class="bg-symph-500 text-white rounded-lg px-4 py-2">
                                {{ isFollowing(trendingUser.id) ? 'Unfollow' : 'Follow' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <div v-else>
                <!-- Si les données n'ont pas encore été chargées -->
                <p>Loading...</p>
            </div>
        </template>
        <template #postForm>
            <PostForm></PostForm>
        </template>
        <div v-for="post in posts" :key="post.id" class="pb-10">
            <!--:href="route('posts.show', {id: post.id})"-->
            <Post
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
                    <div class="flex flex-row gap-2 items-center">
                        <Link as="button" method="post" :href="post.isLiked ? route('posts.unlike', { post: post.id }) : route('posts.like', { post: post.id })" >
                            <Icon icon="iconamoon:heart-duotone" class="w-6 h-6" :class="[ post.isLiked ? 'text-secondary-500' : 'text-gray-300']" />
                        </Link>
                        <h1 class="text-md text-symph-200 font-bold">{{ post.nbLikes }}</h1>
                    </div>
                    <div class="flex flex-row gap-2 items-center">
                        <MainModal @submit="submitComment(post.id)">
                            <template #button>
                                <Icon icon="iconamoon:comment-duotone" class="w-5 h-5 text-gray-300" />
                            </template>
                            <template #modalTitle>
                                Ajouter un commentaire
                            </template>
                            <template #content>
                                <div class="flex flex-col gap-3 px-4 justify-end">
                                    <textarea required v-model="formComment.content" class="w-full rounded-lg"></textarea>
                                    <button class="bg-secondary-500 text-white rounded-lg px-4 py-2">Envoyer</button>
                                </div>
                            </template>
                        </MainModal>
                        <h1 class="text-md text-symph-200 font-bold">{{ post.nbComments }}</h1>
                    </div>
                </template>
            </Post>
            <div v-for="(comment, index) in post.comments" :key="comment.id" class="pl-10 pt-3 flex flex-col w-full items-center relative">
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
                <div v-for="(reply, index) in comment.reply" :key="reply.id" class="px-10 flex flex-col w-full items-center relative">
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
            </div>
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

const followingIds = ref([]);

onMounted(async () => {
    try {
        const response = await router.get(route('user.isFollowing', { id: auth().id() }));
        followingIds.value = response.data.followingIds || []; // Récupérer la liste des suivis
    } catch (error) {
        console.error("Erreur lors du chargement des utilisateurs suivis:", error);
        followingIds.value = []; // Éviter les erreurs d'accès
    }
});

// Fonction pour vérifier si un utilisateur est suivi
const isFollowing = (trendingUserId) => {
    return followingIds.value.includes(trendingUserId); // Retourne `true` si l'utilisateur est suivi
};

// Fonction pour suivre/désabonner avec formulaire
const toggleFollowing = async (trendingUser) => {
    const form = useForm({ trendingUser: trendingUser.id });

    if (isFollowing(trendingUser.id)) {
        form.post(route('user.unfollow'), {
            onSuccess: () => {
                followingIds.value = followingIds.value.filter((id) => id !== trendingUser.id);
            },
        });
    } else {
        form.post(route('user.follow'), {
            onSuccess: () => {
                followingIds.value.push(trendingUser.id);
            },
        });
    }
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
