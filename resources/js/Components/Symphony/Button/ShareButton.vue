<script setup>

import Alerts from "@/Components/Symphony/Alerts.vue";
import {Icon} from "@iconify/vue";
import {useClipboard} from "@vueuse/core";
import {ref} from "vue";

const source = ref('')
const { text, copy, copied, isSupported } = useClipboard({ source })

const props = defineProps({
  copyText: String,
});

</script>

<template>
  <button @click="copy(copyText)" class="text-gray-300">
    <Alerts type="info" :show="copied">Eh oh ! Le liens est copié dans le presse-papier</Alerts>
    <Icon icon="solar:share-line-duotone" class="w-6 h-6" />
  </button>
</template>

<style scoped>

</style>
