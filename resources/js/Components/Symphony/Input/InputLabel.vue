<script setup>

const props = defineProps({
  value: String,
})

const model = defineModel()

</script>

<template>
  <div class="flex flex-col gap-0.5">
    <label for="input" class="text-symph-400">{{ value }}</label>
    <input type="text" class="rounded-md text-gray-400 bg-symph-800 border-symph-400" v-model="model" :placeholder="value">
  </div>
</template>

<style scoped>

</style>
