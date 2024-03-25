<script setup>

const props = defineProps({
    src: String,
});
</script>

<template>
    <div class="relative flex flex-row items-center bg-gray-900 rounded-t-lg p-4 shadow pb-4 gap-4">
        <div class="absolute -left-5 -top-3 w-5 h-full border-b-2 border-symph-400 rounded-bl-xl border-l-2"></div>
        <!-- Informations sur l'utilisateur -->
        <img :src="props.src" class="w-12 h-12 rounded">
        <div class="flex-col flex">
            <h1 class="text-gray-400 truncate text-nowrap"><slot name="name"></slot></h1>
            <span class="text-gray-500 text-sm">
                <slot name="at"></slot>
            </span>
        </div>
    </div>
</template>

<style scoped>

</style>
