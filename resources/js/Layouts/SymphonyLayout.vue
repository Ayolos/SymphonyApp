<script setup>

import Header from "@/Components/Symphony/Header.vue";
import {Link} from "@inertiajs/vue3";
import AlertHandler from "@/Components/Symphony/Alert/AlertHandler.vue";
</script>

<template>
  <div class="max-h-screen h-screen min-h-screen w-screen bg-symph-500">
    <Header class="h-[10vh]"></Header>
    <div>
      <AlertHandler/>
    </div>
    <div class="flex flex-row pr-10 w-full h-[90vh]">
      <div class="h-full px-10 flex-none w-1/4">
        <div class="w-full h-full flex flex-col gap-4">
          <div v-if="$page.props.auth.user" class="flex flex-col items-center bg-symph-800 rounded-lg justify-center">
            <Link :href="route('profileUser.show', {id: $page.props.auth.user.id })"
                  class="hover:bg-symph-600 w-full rounded-t-lg pt-8">
              <img :src="$page.props.auth.user.profile_photo_url" alt="img" class="h-24 mb-2 mx-auto aspect-square rounded-lg">
              <div class="flex flex-col justify-center items-center pb-8">
                <h1 class="md:text-xl text-sm font-bold text-symph-300">{{ $page.props.auth.user.name }}</h1>
                <span class="text-symph-400 text-sm">@{{ $page.props.auth.user.username }}</span>
              </div>
            </Link>
            <div class="grid grid-cols-2 w-full justify-center">
              <div class="flex flex-col items-center border-e border-t border-symph-500 py-4 px-6">
                <h1 class="text-white text-sm">Followers</h1>
                <span class="text-white text-xl font-bold">{{
                    $page.props.nbFollowers
                  }}</span>
              </div>
              <div class="flex flex-col items-center py-4 px-6 border-t border-symph-500">
                <h1 class="text-white text-sm ">Followings</h1>
                <span class="text-white text-xl font-bold">{{
                    $page.props.nbFollowings
                  }}</span>
              </div>
            </div>
          </div>
          <div class="bg-symph-800 rounded-lg px-4 pt-5 mb-4 h-full w-full">
            <h1 class="text-white font-bold text-xl pb-5 truncate">Vous aimeriez aussi...</h1>
            <div class="overflow-y-scroll h-[50vh]">
              <slot name="trendingUsers"/>
            </div>
          </div>
        </div>
      </div>
      <div v-if="$page.props.auth.user" class="flex w-3/4 flex-col gap-5 h-full">
        <slot name="postForm"></slot>
        <slot name="profileButtons"></slot>
        <div class="w-full h-full mb-4 rounded overflow-y-scroll">
          <slot></slot>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>
