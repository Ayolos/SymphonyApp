<script setup>

import SymphonyLayout from "@/Layouts/SymphonyLayout.vue";
import UserInfo from "@/Components/Symphony/Post/UserInfo.vue";
import {Icon} from "@iconify/vue";
import Tooltip from "@/Components/Symphony/Tooltip.vue";

defineProps({
    notifications: Array,
    trendingUsers: Array
})
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
        <div v-if="notifications.length > 0" v-for="notification in notifications" class="flex flex-col gap-4 border-b border-symph-500">
            <div class="flex flex-row gap-2 p-4">
                <img :src="notification.data.user.profile_photo_url" alt="img" class="h-12 rounded-lg">
                <div class="">
                  <UserInfo :userId="notification.data.user.id" :name="notification.data.user.name" :username="notification.data.user.username" />
                  <p v-if="notification.data.post" class="text-symph-100 py-2">{{ notification.data.post.content }}</p>
                  <p class="text-gray-500">{{ notification.data.message }}</p>
                </div>
            </div>
        </div>
      <div v-else>
          <div class="flex flex-col items-center justify-center w-full h-full py-20">
              <Icon icon="line-md:alert-circle-twotone-loop" class="w-48 h-48 text-gray-500"/>
              <h1 class="text-2xl text-symph-200 font-bold">Aucunes Notifications</h1>
          </div>
      </div>
  </SymphonyLayout>
</template>

<style scoped>

</style>
