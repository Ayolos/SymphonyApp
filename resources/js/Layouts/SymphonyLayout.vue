<script setup>

import Header from "@/Components/Symphony/Header.vue";
import BurgerMenu from "@/Components/Symphony/BurgerMenu.vue";
import {Icon} from "@iconify/vue";
import {reactive} from "vue";
import {router, useForm, Link} from "@inertiajs/vue3";


</script>

<template>
    <div class="max-h-screen h-screen min-h-screen bg-symph-500">
        <Header class="mb-5"></Header>
        <div class="flex flex-row pr-10">
            <div class="w-[30vw] inl h-full px-10">
                <div class="w-full h-full flex flex-col gap-4">
                    <div v-if="$page.props.auth.user" class="flex flex-col items-center bg-symph-800 rounded-lg justify-center">
                        <Link :href="route('profile')" class="hover:bg-symph-600 w-full rounded-t-lg pt-8">
                            <img :src="$page.props.auth.user.profile_photo_url" alt="img" class="h-24 mb-2 mx-auto rounded-lg">
                            <div class="flex flex-col justify-center items-center pb-8">
                                <h1 class="md:text-xl text-sm font-bold text-symph-300">{{ $page.props.auth.user.name }}</h1>
                                <span class="text-symph-400 text-sm">@{{ $page.props.auth.user.username }}</span>
                            </div>
                        </Link>
                        <div class="grid grid-cols-2 w-full justify-center">
                            <div class="border-e border-t border-symph-500 py-4 px-6">
                                <h1 class="text-white text-sm">Followers</h1>
                                <span class="text-white text-xl font-bold">{{ $page.props.auth.user.followers ? $page.props.auth.user.followings : 0 }}</span>
                            </div>
                            <div class="py-4 px-6 border-t border-symph-500">
                                <h1 class="text-white text-sm ">Followings</h1>
                                <span class="text-white text-xl font-bold">{{ $page.props.auth.user.followings ? $page.props.auth.user.followings : 0 }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="bg-symph-800 rounded-lg px-4 py-5 w-full">
                        <slot name="trendingUsers"/>
                    </div>
                </div>
            </div>
                <div class="w-full flex flex-col gap-10 h-[85vh]" v-if="$page.props.auth.user">
                    <slot name="postForm">
                    </slot>
                    <div class="w-full rounded overflow-y-scroll">
                        <slot></slot>
                    </div>
                </div>
        </div>
        </div>
</template>

<style scoped>

</style>
