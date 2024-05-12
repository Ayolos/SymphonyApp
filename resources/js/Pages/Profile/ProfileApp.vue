<script setup xmlns="http://www.w3.org/1999/html">

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
import UserInfo from "@/Components/Symphony/Post/UserInfo.vue";
import UserCommentInfo from "@/Components/Symphony/UserCommentInfo.vue";
import Alerts from "@/Components/Symphony/Alerts.vue";
import Tooltip from "@/Components/Symphony/Tooltip.vue";

defineProps({
  posts: Object,
  user: Object,
  nbPosts: Number,
  likedPosts: Object,
  followers: Object,
  followings: Object,
  trendingUsers: Array,
});

const source = ref('')
const {text, copy, copied, isSupported} = useClipboard({source})

const formComment = useForm({
  content: "",
  post_id: null,
});

const formFollow = useForm({
  following_id: null,
});
const submitComment = () => {
  formComment.post(route('comments.store'), {
    preserveScroll: true,
    onSuccess: () => {
      formComment.reset('content');
    }
  });
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

const alertFollow = ref(false);
const alertUnFollow = ref(false);

// Fonction pour suivre/désabonner avec formulaire
const toggleFollowing = (trendingUser) => {
  formFollow.following_id = trendingUser.id;
  formFollow.post(route('user.follow'), {
    preserveScroll: true,
    onSuccess: () => {
      alertFollow.value = true;
      setTimeout(() => {
        alertFollow.value = false;
      }, 2000);
    }
  })
};

const toggleUnFollow = (trendingUser) => {
  formFollow.following_id = trendingUser.id;
  formFollow.delete(route('user.unfollow', {user: trendingUser.id}), {
    preserveScroll: true,
    onSuccess: () => {
      console.log('user retiré')
      alertUnFollow.value = true;
      setTimeout(() => {
        alertUnFollow.value = false;
      }, 2000);
    }
  })
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
    <template #postForm>
      <div>
        <div class="w-full bg-symph-700 flex gap-8 border-b pb-8 border-symph-500 sm:flex-row flex-col rounded-t-2xl border-x border-t px-8 pt-8">
          <img :src="user.profile_photo_url" class="shadow-symph-500 aspect-square self-center sm:min-w-48 min-w-20 h-max rounded">
          <div class="justify-between w-full flex flex-col text-gray-500 mr-8">
              <div class="flex flex-col">
                  <div class="flex flex-row gap-5 items-center justify-between">
                      <div class="">
                          <p class="font-bold text-lg">{{ user.name }}</p>
                          <p class="text-sm">@{{ user.username }}</p>
                      </div>
                      <div v-if="user.id !== $page.props.auth.user.id">
                          <form v-if="user.isFollowed" @submit.prevent="toggleUnFollow(user)">
                              <button type="submit" class="bg-red-900/70 hover:bg-red-900/40 text-symph-100 border border-red-500 rounded-lg p-2">
                                  <Icon icon="line-md:person-off-twotone" class="text-2xl"></Icon>
                              </button>
                          </form>
                          <form v-else @submit.prevent="toggleFollowing(user)">
                              <button type="submit" class="bg-secondary-900/70 hover:bg-secondary-900/40 text-symph-100 border border-secondary rounded-lg p-2">
                                  <Icon icon="line-md:person-add-twotone" class="text-2xl"></Icon>
                              </button>
                          </form>
                      </div>
                  </div>
                  <div class="pt-6 break-all ">
                      <div class="text-sm">{{ user.description }}</div>
                  </div>
              </div>
            <div class="flex sm:flex-row gap-2 flex-col sm:gap-10 gap-2 items-center-5 pt-6">
              <div class="flex flex-row items-center gap-2">
                <Icon class="w-6 h-6" icon="material-symbols-light:post-add"/>
                <p class="text-sm font-light text-gray-700">{{ nbPosts }} Posts</p>
              </div>
              <div class="flex flex-row gap-2 items-center">
                <Icon class="min-w-6 h-6" icon="iconoir:calendar"/>
                <p class="text-sm font-light text-gray-700">A rejoint <span
                    class="font-bold text-secondary">Symphony</span> le {{ dateFormater(user.created_at) }}</p>
              </div>
            </div>
          </div>
        </div>
        <div class="flex text-symph-400 bg-symph-700 flex-row justify-around items-center rounded-b-2xl border-symph-500 border-x border-b">
          <button @click="ManageShowFilter('post')" :class="showFilter === 'post' ? 'text-secondary' : ''" class="hover:bg-gray-900 rounded-bl-2xl font-black border-r border-gray-900 text-center basis-1/2 h-full p-5">
            <div class="flex flex-row gap-2 items-center justify-center">
              <Icon class="w-6 h-6" icon="material-symbols-light:post-outline-rounded"/>
              <h1 class="text-md hidden sm:block font-bold">Posts</h1>
            </div>
          </button>
          <button @click="ManageShowFilter('like')" :class="showFilter === 'like' ? 'text-secondary' : ''" class="hover:bg-gray-900 font-black border-r border-gray-900 text-center basis-1/2 h-full p-5">
            <div class="flex flex-row gap-2 items-center justify-center">
              <Icon class="w-6 h-6" icon="icon-park-twotone:like"/>
              <h1 class="text-md hidden sm:block font-bold">J'aime</h1>
            </div>
          </button>
          <button @click="ManageShowFilter('followers')" :class="showFilter === 'followers' ? 'text-secondary' : ''" class="hover:bg-gray-900 font-black border-r border-gray-900 text-center basis-1/2 h-full p-5">
            <div class="flex flex-row gap-2 items-center justify-center">
              <Icon class="w-6 h-6" icon="solar:user-circle-bold-duotone"/>
              <h1 class="text-md hidden sm:block font-bold">Followers</h1>
            </div>
          </button>
          <button @click="ManageShowFilter('followings')" :class="showFilter === 'followings' ? 'text-secondary' : ''" class="hover:bg-gray-900 rounded-br-2xl font-black text-center basis-1/2 h-full p-5">
            <div class="flex flex-row gap-2 items-center justify-center">
              <Icon class="w-6 h-6" icon="solar:user-circle-bold-duotone"/>
              <h1 class="text-md hidden sm:block font-bold">Followings</h1>
            </div>
          </button>
        </div>
      </div>
    </template>
    <div v-if="showFilter === 'post'">
      <div v-for="post in posts" v-if="nbPosts !== 0" :key="post.id">
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
      </div>
      <div v-else>
        <div class="flex flex-col text-symph-300 items-center justify-center py-20">
          <Icon class="w-48 h-48" icon="line-md:cancel-twotone"/>
          <h1 class="text-3xl text-center font-bold">Aucun post pour le moment</h1>
        </div>
      </div>
    </div>
    <div v-else-if="showFilter === 'like'">
      <div v-for="post in likedPosts" v-if="likedPosts.length !== 0" :key="post.id">
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
      </div>
      <div v-else>
        <div class="flex flex-col text-symph-300 items-center justify-center py-20">
          <Icon class="w-48 h-48" icon="line-md:person-off-twotone-loop"/>
          <h1 class="text-3xl text-center font-bold">Aucun post liké pour le moment</h1>
        </div>
      </div>
    </div>
    <div v-else-if="showFilter === 'followers'" class="flex-col flex gap-2">
      <div v-if="followers.length !== 0" class="w-full">
        <div v-for="user in followers" class="hover:bg-symph-600 py-3 px-8">
          <Link :href="route('profileUser.show', {id: user.id})">
            <div class="flex flex-row gap-4 items-center">
              <img :src="user.profile_photo_url" class="aspect-square rounded h-10">
              <div class="flex flex-col">
                <h1 class="text-gray-600 truncate text-nowrap">{{ user.name }}</h1>
                <p class="text-gray-500 text-sm">@{{ user.username }}</p>
              </div>
            </div>
          </Link>
        </div>
      </div>
      <div v-else class="flex flex-col items-center py-20">
        <Icon icon="line-md:alert-circle-twotone-loop" class="w-48 h-48 text-gray-500"/>
        <h1 class="text-gray-600 text-3xl text-center font-bold truncate text-nowrap">Aucun résultat</h1>
      </div>
    </div>
    <div v-else-if="showFilter === 'followings'" class="flex-col flex gap-2">
      <div v-if="followings.length !== 0" class="w-full">
        <div v-for="user in followings" class="hover:bg-symph-600 py-3 px-8">
          <Link :href="route('profileUser.show', {id: user.id})">
            <div class="flex flex-row gap-4 items-center">
              <img :src="user.profile_photo_url" class="aspect-square rounded h-10">
              <div class="flex flex-col">
                <h1 class="text-gray-600 truncate text-nowrap">{{ user.name }}</h1>
                <p class="text-gray-500 text-sm">@{{ user.username }}</p>
              </div>
            </div>
          </Link>
        </div>
      </div>
      <div v-else class="flex flex-col items-center py-20">
        <Icon icon="line-md:alert-circle-twotone-loop" class="w-48 h-48 text-gray-500"/>
        <h1 class="text-gray-600 text-3xl text-center font-bold truncate text-nowrap">Aucun résultat</h1>
      </div>
    </div>
  </SymphonyLayout>

</template>

<style scoped>

</style>
