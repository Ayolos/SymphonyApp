<script setup>

import SymphonyLayout from "@/Layouts/SymphonyLayout.vue";

import {ref, watch} from "vue";
import {router, Link} from "@inertiajs/vue3";
import PostInfo from "@/Components/Symphony/PostInfo.vue";

defineProps({
  searchResults: Object,
});

const search = ref("");
const isLoading = ref(false); // Loading state

watch(search, async (value) => {
  isLoading.value = true; // Set loading state to true when fetching starts
  try {
    await router.get(route('search.index'), {
      search: search.value,
    }, {
      preserveState: true,
    });
  } finally {
    isLoading.value = false; // Set loading state to false when fetching ends
  }
});

</script>

<template>
  <SymphonyLayout>
    <div class="flex flex-col w-full">
      <h1 class="text-3xl flex text-symph-100">Rechercher un utilisateur</h1>
      <div class="flex flex-col items-center mt-5">
        <input type="search" class="bg-symph-800 text-gray-500 p-3 shadow border-0 rounded-lg w-full" v-model="search" placeholder="Rechercher un utilisateur">
        <div v-if="searchResults !== null" class="bg-symph-100 shadow w-full rounded-xl mt-3 max-h-[70vh] h-max overflow-y-scroll">
          <div v-for="search in searchResults" class="hover:bg-gray-300 py-3 px-8">
            <Link :href="route('profileUser.show', {id: search.id})">
              <div class="flex flex-row gap-4 items-center">
                <img :src="search.profile_photo_url" class="aspect-square rounded h-10">
                <div class="flex flex-col">
                  <h1 class="text-gray-600 truncate text-nowrap">{{ search.name }}</h1>
                  <p class="text-gray-500 text-sm">{{ search.username }}</p>
                </div>
              </div>
            </Link>
          </div>
        </div>
        <div v-else class=" pt-4">
            <h1 class="text-gray-600 text-2xl truncate text-nowrap">Aucun résultat</h1>
        </div>
      </div>
    </div>
  </SymphonyLayout>
</template>

<style scoped>

</style>
