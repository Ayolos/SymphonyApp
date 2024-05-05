<script setup>

import SymphonyLayout from "@/Layouts/SymphonyLayout.vue";
import Logo from "/public/Logo.png";
import {Link, useForm} from "@inertiajs/vue3";
import {Icon} from "@iconify/vue";
import Modal from "@/Components/Symphony/Modal/Modal.vue";
import Post from "@/Components/Symphony/Post/Post.vue";
import {onMounted, ref} from "vue";
import MainModal from "@/Components/Symphony/Modal/MainModal.vue";
import PlayerAudio from "@/Components/Symphony/PlayerAudio.vue";
import {useClipboard} from "@vueuse/core";

defineProps({
    posts: Object,
    user: Object,
    nbPosts: Number,
});

const source = ref('')
const { text, copy, copied, isSupported } = useClipboard({ source })

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

const dateFormater = (date) => {
    return new Date(date).toLocaleDateString('fr-FR', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

</script>

<template>
    <SymphonyLayout>
        <template #postForm>
            <div class="w-full flex flex-row bg-symph-900 rounded-lg h-[25vh]">
                <img :src="user.profile_photo_url" class="aspect-square h-full rounded-l">
                <div class="w-full flex flex-col text-gray-500 p-8">
                  <div class="flex flex-row gap-5">
                    <div class="">
                      <p class="font-bold text-lg">{{ user.name }}</p>
                      <p class="text-sm">@{{ user.username }}</p>
                    </div>
                  </div>
                    <div class="pt-6 w-full">
                      <p class="text-sm text-ellipsis overflow-hidden w-80">Lorem ipsumzeff zefzfdfvdfvdfvvdfvdfvdfvdfvdfvdfvdfvdfvdfvfdvdfvdfvdfvdfvdfefzfdfvdfvdfvvdfvdfvdfvdfvdfvdfvdfvdfvdfvfdvdfvdfvdfvdfvdfefzfdfvdfvdfvvdfvdfvdfvdfvdfvdfvdfvdfvdfvfdvdfvdfvdfvdfvdfefzfdfvdfvdfvvdfvdfvdfvdfvdfvdfvdfvdfvdfvfdvdfvdfvdfvdfvdfefzfdfvdfvdfvvdfvdfvdfvdfvdfvdfvdfvdfvdfvfdvdfvdfvdfvdfvdfefzfdfvdfvdfvvdfvdfvdfvdfvdfvdfvdfvdfvdfvfdvdfvdfvdfvdfvdfvfdvdvdvz fzfzfz fffeferfegre egerergergerger egergerge</p>
                    </div>
                  <div class="flex flex-row gap-10 items-center-5 pt-6">
                    <div class="flex flex-row items-center gap-2">
                      <Icon icon="material-symbols-light:post-add" class="w-6 h-6" />
                      <p class="text-sm font-light text-gray-700">{{ nbPosts }} Posts</p>
                    </div>
                    <div class="flex flex-row gap-2 items-center">
                      <Icon icon="iconoir:calendar" class="w-5 h-5" />
                      <p class="text-sm font-light text-gray-700">A rejoint <span class="font-bold text-secondary">Symphony</span> le {{ dateFormater(user.created_at) }}</p>
                    </div>
                  </div>
                </div>
            </div>

        </template>
      <div v-if="nbPosts !== 0" v-for="post in posts" :key="post.id" class="pb-5">
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
      </div>
      <div v-else>
        <div class="flex flex-col text-symph-300 items-center justify-center pt-20">
          <Icon icon="line-md:cancel-twotone" class="w-48 h-48" />
          <h1 class="text-3xl font-bold">Aucun post pour le moment</h1>
        </div>
      </div>
    </SymphonyLayout>

</template>

<style scoped>

</style>
