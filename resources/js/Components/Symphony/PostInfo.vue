<script setup>

const props = defineProps({
    src: String,
});
</script>

<template>
    <div class="flex flex-row items-center pb-4 gap-4">
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
