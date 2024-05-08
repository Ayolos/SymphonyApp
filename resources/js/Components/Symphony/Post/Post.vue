<script setup>

import PostInfo from "@/Components/Symphony/PostInfo.vue";
import {router, useForm, Link} from "@inertiajs/vue3";
import {watch} from "vue";

defineProps({
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
    <div class="bg-symph-900 rounded-lg z-0 h-full w-full shadow-xl">
            <!-- Contenu du post -->
            <PostInfo :src="src" :userId="userId" :connect-line="connectLine" :date="formatDateDifference(createdAt)">
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
        </div>
    </div>
</template>

<style scoped>

</style>
