<script setup>

import {Link} from "@inertiajs/vue3";
import {computed} from "vue";

const props = defineProps({
    name: String,
    username: String,
    userId: {
        type: Number,
        required: false
    }
})

const linkToProfile = computed(() => {
    return props.userId ? route('profileUser.show', {id: props.userId}): route().current();
})

</script>

<template>
    <div class="flex flex-row gap-1">
        <Link :href="linkToProfile">
            <h1 class="text-gray-100 font-bold truncate text-nowrap hover:underline">{{ name }}</h1>
        </Link>
        <Link :href="linkToProfile">
            <span class="text-symph-400">@{{ username }}</span>
        </Link>
    </div>
</template>

<style scoped>

</style>
