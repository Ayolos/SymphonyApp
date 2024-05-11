<script setup>

import SymphonyLayout from "@/Layouts/SymphonyLayout.vue";

defineProps({
    notifications: Object
})
</script>

<template>
  <SymphonyLayout>
        <div v-for="notification in notifications" class="flex flex-col gap-4 bg-symph-100">
            <div class="flex flex-row gap-2 p-4 border-b-symph-200 border">
                <img :src="notification.data.user.profile_photo_url" alt="img" class="h-12 rounded-lg">
                <div class="">
                  <h2 class="font-black text-symph-500">@{{ notification.data.user.username }}</h2>
                  <p v-if="notification.data.post" class="text-gray-600 py-2">{{ notification.data.post.content }}</p>
                  <p class="text-gray-600">{{ notification.data.message }}</p>
                </div>
            </div>
        </div>
  </SymphonyLayout>
</template>

<style scoped>

</style>
