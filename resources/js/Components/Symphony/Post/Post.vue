<script setup>

import PostInfo from "@/Components/Symphony/PostInfo.vue";
import {router, useForm, Link} from "@inertiajs/vue3";
import {computed, watch} from "vue";
import UserInfo from "@/Components/Symphony/Post/UserInfo.vue";
import FormatDatePost from "@/Components/Symphony/Post/FormatDatePost.vue";
import PlayerAudio from "@/Components/Symphony/PlayerAudio.vue";

const props =defineProps({
    line: {
        type: Boolean,
        required: false,
        default: false
    },
    border: {
        type: Boolean,
        required: false,
        default: true
    },
    post: Object,
    connectLine: {
      type: Boolean,
      required: false,
      default: true
    },
    createdAt: String,
    src: String,
    userId: {
        type: Number,
        required: false
    }
});

const borderClass = computed(() => {
    return props.border ? 'border-b border-symph-500' : ''
});

const formatDateDifference = (createdAt) => {
  const createdDate = new Date(createdAt);
  const currentDate = new Date();

  const timeDifference = currentDate.getTime() - createdDate.getTime();
  const secondsDifference = Math.floor(timeDifference / 1000);
  const minutesDifference = Math.floor(secondsDifference / 60);
  const hoursDifference = Math.floor(minutesDifference / 60);
  const daysDifference = Math.floor(hoursDifference / 24);

  if (daysDifference >= 7) {
    return formatDateString(createdDate);
  } else if (hoursDifference > 0) {
    return `${hoursDifference} h`;
  } else if (minutesDifference > 0) {
    return `${minutesDifference} m`;
  } else {
    return `${secondsDifference} s`;
  }
};

const formatDateString = (date) => {
  const day = date.getDate();
  const month = date.getMonth() + 1; // Month is zero-based
  const year = date.getFullYear();

  return `${day}/${month}/${year}`;
};
</script>

<template>
    <div class="z-0 h-full p-5 flex flex-row w-full justify-between gap-4" :class="borderClass">
        <div class="flex flex-row">
            <Link :href="userId ? route('profileUser.show', {id: userId}): route().current()" class="mr-3 z-0 flex-none relative">
                <img :src="src" class="w-max h-12 rounded">
            </Link>
            <div class="flex flex-col">
                <div class="flex flex-row items-start gap-2">
                    <UserInfo
                        :userId="post.user.id"
                        :name="post.user.name"
                        :username="post.user.username"
                    />
                    <FormatDatePost class="" :createdAt="createdAt" />
                </div>
                <p class="text-symph-100">{{post.content}}</p>
                <div class="flex-row flex items-end text-sm gap-8 h-8">
                    <slot name="likeButton"></slot>
                </div>
            </div>
        </div>
        <div v-if="post.song" class="md:pr-20 pr-0">
            <PlayerAudio :song="post.song"></PlayerAudio>
        </div>
            <!-- Contenu du post -->
<!--            <PostInfo :src="src" :userId="userId" :connect-line="connectLine" :date="formatDateDifference(createdAt)">
                <template #name>
                    <slot name="name"></slot>
                </template>
                <template #at>
                    <slot name="at"></slot>
                </template>
            </PostInfo>
            <div class="pt-5 px-10 flex flex-row justify-between items-start">
                <p class="text-md text-gray-500 pt-8 break-words w-full">
                    <slot name="content"></slot>
                </p>
                <div class="z-50 pr-10">
                  <slot name="media"></slot>
                </div>
            </div>
        <div class="flex-row flex gap-8 px-6 pt-5 pb-10">
            <slot name="likeButton"></slot>
        </div>-->
    </div>
</template>

<style scoped>

</style>
