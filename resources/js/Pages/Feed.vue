
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
                    <div class="flex flex-col gap-3 px-4">
                      <textarea required v-model="formComment.content" maxlength="255" placeholder="Ecrit ton commentaire" class="w-full text-symph-200 h-48 rounded-lg bg-symph-800 border-symph-500 resize-none"></textarea>
                      <CounterMessage class="text-symph-100 w-full text-end" :message="formComment.content" :max-characters="255" />
                      <button class="bg-secondary-500 text-white rounded-lg px-4 py-2">Envoyer</button>
                    </div>
                  </template>
                </MainModal>
                <h1 class="text-md text-symph-200 font-bold">{{ post.nbComments }}</h1>
              </div>
              <!-- Share button -->
              <div class="flex flex-row gap-2 items-center">
                <button @click="copy(route('posts.show', {id: post.id}))" class="text-gray-300">
                  <Alerts type="info" :show="copied">Eh oh ! Le liens est copié dans le presse-papier</Alerts>
                  <Icon icon="solar:share-line-duotone" class="w-6 h-6" />
                </button>
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
