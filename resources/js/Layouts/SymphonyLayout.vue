<script setup>

import Header from "@/Components/Symphony/Header.vue";
import BurgerMenu from "@/Components/Symphony/BurgerMenu.vue";
import {Icon} from "@iconify/vue";
import {reactive} from "vue";
import {router, useForm} from "@inertiajs/vue3";

const formPost = useForm({
    content: '',
    title: 'Test'
})

const submitPost = async () => {
    await formPost.post(route('posts.store'), {
        onSuccess: () => {
            formPost.reset('content');
        }
    });
};

</script>

<template>
    <div class="max-h-screen min-h-screen bg-symph-500">
        <Header class="mb-5"></Header>
        <div class="flex flex-row pr-10">
            <div class="w-[30vw] inl h-full px-10">
                <div class="w-full h-full flex flex-col gap-4">
                    <div v-if="$page.props.auth.user" class="flex flex-col items-center bg-symph-800 rounded-lg justify-center pt-8">
                        <img :src="$page.props.auth.user.profile_photo_url" alt="img" class="h-24 mb-2 rounded-lg">
                        <div class="flex flex-col justify-center items-center pb-8">
                            <h1 class="md:text-xl text-sm font-bold text-symph-300">{{ $page.props.auth.user.name }}</h1>
                            <span class="text-symph-400 text-sm">@{{ $page.props.auth.user.username }}</span>
                        </div>
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
                        <h1 class="text-white font-bold text-xl pb-5">Vous aimeriez aussi...</h1>
                        <div v-for="i in 7" class="flex flex-row gap-4 justify-between">
                            <div class="flex flex-row items-center pb-4 gap-4">
                                <img src="https://fakeimg.pl/300/" class="w-12 h-12 rounded">
                                <div class="flex-col flex">
                                    <h1 class="text-gray-400 truncate text-nowrap">Antoine ANDRE</h1>
                                    <span class="text-gray-500 text-sm">@antoineandre</span>
                                </div>
                            </div>
                            <div class="flex flex-row items-center gap-4 pr-4">
                                <Icon icon="gridicons:add" class="text-symph-200 text-3xl"></Icon>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <form @submit.prevent="submitPost">
                <div class="w-full" v-if="$page.props.auth.user">
                    <div class="bg-symph-100 rounded-lg">
                        <div class="flex flex-row justify-between items-start h-48">
                            <div class="flex-col items-center gap-4 p-4 w-full h-full">
                                <div class="flex flex-row items-center gap-4 p-4">
                                    <img src="https://fakeimg.pl/300/" class="w-12 h-12 rounded">
                                    <div class="flex-col flex">
                                        <h1 class="text-gray-400 truncate text-nowrap">{{ $page.props.auth.user.name }}</h1>
                                        <span class="text-gray-500 text-sm">@antoineandre</span>
                                    </div>
                                </div>
                                <div class="pl-4 pr-20 flex flex-row justify-between items-end gap-8">
                                    <!-- Définir la taille du textarea -->
                                    <textarea v-model="formPost.content" class="w-full h-20 focus:ring-0 resize-none border-0 p-0 rounded-lg bg-symph-100" placeholder="Exprimez-vous..."></textarea>
                                    <div>
                                        <button class="bg-symph-500 text-white rounded-lg px-4 py-2">Publier</button>
                                    </div>
                                </div>
                            </div>
                            <img src="https://fakeimg.pl/300/" class="h-48 w-max object-cover rounded-e-lg">
                        </div>
                    </div>
                </div>
            </form>
        </div>
        </div>
</template>

<style scoped>

</style>
