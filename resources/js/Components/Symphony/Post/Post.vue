<script setup>

import PostInfo from "@/Components/Symphony/PostInfo.vue";

defineProps({
    href: String,
    src: String,
});

</script>

<template>
    <div class="bg-symph-900 rounded-lg">
        <a :href="href" class="flex flex-col gap-2">
            <!-- Contenu du post -->
            <PostInfo :src="src">
                <template #name>
                    <slot name="name"></slot>
                </template>
                <template #at>
                    <slot name="at"></slot>
                </template>
            </PostInfo>
            <div class="py-10 px-10">
                <p class="text-md text-gray-500">
                    <slot name="content"></slot>
                </p>
            </div>
        </a>
        <div class="flex-row flex gap-8 px-6 pt-5 pb-10">
            <slot name="likeButton"></slot>
        </div>
    </div>
</template>

<style scoped>

</style>
