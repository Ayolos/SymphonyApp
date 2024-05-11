<script setup>

import SymphonyLayout from "@/Layouts/SymphonyLayout.vue";
import UserInfo from "@/Components/Symphony/Post/UserInfo.vue";

defineProps({
    notifications: Object
})
</script>

<template>
  <SymphonyLayout>
        <div v-for="notification in notifications" class="flex flex-col gap-4 border-b border-symph-500">
            <div class="flex flex-row gap-2 p-4">
                <img :src="notification.data.user.profile_photo_url" alt="img" class="h-12 rounded-lg">
                <div class="">
                  <UserInfo :userId="notification.data.user.id" :name="notification.data.user.name" :username="notification.data.user.username" />
                  <p v-if="notification.data.post" class="text-symph-100 py-2">{{ notification.data.post.content }}</p>
                  <p class="text-gray-500">{{ notification.data.message }}</p>
                </div>
            </div>
        </div>
  </SymphonyLayout>
</template>

<style scoped>

</style>
