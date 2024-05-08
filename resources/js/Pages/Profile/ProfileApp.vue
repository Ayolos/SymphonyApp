<script setup>

import SymphonyLayout from "@/Layouts/SymphonyLayout.vue";
import {Link, useForm} from "@inertiajs/vue3";
import {Icon} from "@iconify/vue";
import Post from "@/Components/Symphony/Post/Post.vue";
import {ref} from "vue";
import MainModal from "@/Components/Symphony/Modal/MainModal.vue";
import PlayerAudio from "@/Components/Symphony/PlayerAudio.vue";
import {useClipboard} from "@vueuse/core";
import ShareButton from "@/Components/Symphony/Button/ShareButton.vue";
import CounterMessage from "@/Components/Symphony/CounterMessage.vue";
import UserInfo from "@/Components/Symphony/UserInfo.vue";

defineProps({
  posts: Object,
  user: Object,
  nbPosts: Number,
  likedPosts: Object,
});

const source = ref('')
const {text, copy, copied, isSupported} = useClipboard({source})

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

const showFilter = ref('post');

const ManageShowFilter = (filter) => {
  showFilter.value = filter;
};

</script>

<template>
  <SymphonyLayout>
    <template #postForm>
      <div>
        <div class="w-full flex border-b pb-8 border-gray-900 flex-row bg-symph-900 rounded-t-lg pl-8 pt-8">
          <img :src="user.profile_photo_url" class="shadow-symph-500 shadow-2xl aspect-square h-48 rounded">
          <div class="w-full h-max flex flex-col text-gray-500 pt-8 px-8">
            <div class="flex flex-row gap-5 items-center justify-between">
              <div class="">
                <p class="font-bold text-lg">{{ user.name }}</p>
                <p class="text-sm">@{{ user.username }}</p>
              </div>
              <button type="button" class="bg-secondary-900/70 hover:bg-secondary-900/40 text-symph-100 border border-secondary rounded-lg p-2">
                <Icon icon="line-md:person-add-twotone" class="text-2xl"></Icon>
              </button>
            </div>
            <div class="pt-6 w-full">
              <p class="text-sm text-ellipsis overflow-hidden w-80">{{ user.description }}</p>
            </div>
            <div class="flex flex-row gap-10 items-center-5 pt-6">
              <div class="flex flex-row items-center gap-2">
                <Icon class="w-6 h-6" icon="material-symbols-light:post-add"/>
                <p class="text-sm font-light text-gray-700">{{ nbPosts }} Posts</p>
              </div>
              <div class="flex flex-row gap-2 items-center">
                <Icon class="w-5 h-5" icon="iconoir:calendar"/>
                <p class="text-sm font-light text-gray-700">A rejoint <span
                    class="font-bold text-secondary">Symphony</span> le {{ dateFormater(user.created_at) }}</p>
              </div>
            </div>
          </div>
        </div>
        <div class="flex text-symph-400 flex-row justify-around items-center rounded-b bg-symph-900">
          <div @click="ManageShowFilter('post')" :class="showFilter === 'post' ? 'text-secondary' : ''" class="hover:bg-gray-900 font-black border-r border-gray-900 text-center basis-1/2 h-full p-5">Post</div>
          <div @click="ManageShowFilter('like')" :class="showFilter === 'like' ? 'text-secondary' : ''" class="hover:bg-gray-900 font-black text-center basis-1/2 h-full p-5">J'aime</div>
        </div>
      </div>
    </template>
    <div v-if="showFilter === 'post'">
      <div v-for="post in posts" v-if="nbPosts !== 0" :key="post.id" class="pb-5">
        <Post
            :createdAt="post.created_at"
            :src="post.user.profile_photo_url"
            :user-id="post.user.id">
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
                <Icon class="w-6 h-6" icon="icon-park-twotone:more-three"/>
                <h1 class="text-md font-bold">voir plus</h1>
              </div>
            </Link>
            <div class="flex flex-row gap-2 items-center">
              <Link :href="post.isLiked ? route('posts.unlike', { post: post.id }) : route('posts.like', { post: post.id })" as="button"
                    method="post">
                <Icon :class="[ post.isLiked ? 'text-secondary-500' : 'text-gray-300']" class="w-6 h-6 transition hover:scale-110 hover:rotate-6 ease-in-out"
                      icon="iconamoon:heart-duotone"/>
              </Link>
              <h1 class="text-md text-symph-200 font-bold">{{ post.nbLikes }}</h1>
            </div>
            <div class="flex flex-row gap-2 items-center">
              <MainModal @submit="submitComment(post.id)">
                <template #button>
                  <Icon class="w-5 h-5 transition hover:scale-110 ease-in-out text-gray-300"
                        icon="iconamoon:comment-duotone"/>
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
      <div v-else>
        <div class="flex flex-col text-symph-300 items-center justify-center pt-20">
          <Icon class="w-48 h-48" icon="line-md:cancel-twotone"/>
          <h1 class="text-3xl font-bold">Aucun post pour le moment</h1>
        </div>
      </div>
    </div>
    <div v-else-if="showFilter === 'like'">
      <div v-for="post in likedPosts" v-if="likedPosts.length !== 0" :key="post.id" class="pb-5">
        <Post
            :createdAt="post.created_at"
            :src="post.user.profile_photo_url"
            :user-id="post.user.id">
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
                <Icon class="w-6 h-6" icon="icon-park-twotone:more-three"/>
                <h1 class="text-md font-bold">voir plus</h1>
              </div>
            </Link>
            <div class="flex flex-row gap-2 items-center">
              <Link :href="post.isLiked ? route('posts.unlike', { post: post.id }) : route('posts.like', { post: post.id })" as="button"
                    method="post">
                <Icon :class="[ post.isLiked ? 'text-secondary-500' : 'text-gray-300']" class="w-6 h-6 transition hover:scale-110 hover:rotate-6 ease-in-out"
                      icon="iconamoon:heart-duotone"/>
              </Link>
              <h1 class="text-md text-symph-200 font-bold">{{ post.nbLikes }}</h1>
            </div>
            <div class="flex flex-row gap-2 items-center">
              <MainModal @submit="submitComment(post.id)">
                <template #button>
                  <Icon class="w-5 h-5 transition hover:scale-110 ease-in-out text-gray-300"
                        icon="iconamoon:comment-duotone"/>
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
      <div v-else>
        <div class="flex flex-col text-symph-300 items-center justify-center pt-20">
          <Icon class="w-48 h-48" icon="line-md:person-off-twotone-loop"/>
          <h1 class="text-3xl font-bold">Aucun post liké pour le moment</h1>
        </div>
      </div>
    </div>
  </SymphonyLayout>

</template>

<style scoped>

</style>
