<script setup>

import SymphonyLayout from "@/Layouts/SymphonyLayout.vue";

import {ref, watch} from "vue";
import {router} from "@inertiajs/vue3";


defineProps({
  searchResults: Object,
});

const search = ref("");

watch(search, (value) => {
  console.log(value);
  router.get(route('search.index'), {
    search: search.value,
  }, {
    preserveState: true,
  });
});
</script>

<template>
  <SymphonyLayout>
    <div class="flex flex-col items-center">
      <h1 class="text-4xl font-bold mt-10">Search</h1>
      <div class="flex flex-col items-center mt-10">
        <input type="text" class="border-2 border-secondary-500 rounded-lg p-2 w-96" v-model="search" placeholder="Search for posts">
        <div v-for="search in searchResults" class="pt-2">
          <div class="flex flex-row gap-2">
            <p>{{search.name}}</p>
            <p>@{{search.username}}</p>
          </div>
        </div>
      </div>
    </div>
  </SymphonyLayout>
</template>

<style scoped>

</style>
