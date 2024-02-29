<script setup>

import SymphonyLayout from "@/Layouts/SymphonyLayout.vue";
import Logo from "/public/Logo.png";
</script>

<template>
    <SymphonyLayout>
        <div class="w-full flex flex-row bg-symph-900 rounded-lg h-[30vh]">
            <img :src="Logo" class="h-full aspect-square">
            <div class="w-full flex flex-col items-center text-gray-500">
                <h1 class="text-gray-500">Profile</h1>
                <p>{{ $page.props.auth.user.name }}</p>
                <p>@{{ $page.props.auth.user.username }}</p>
            </div>
        </div>
    </SymphonyLayout>

</template>

<style scoped>

</style>
