<script setup>

import Alerts from "@/Components/Symphony/Alerts.vue";
import {Icon} from "@iconify/vue";
import {useClipboard} from "@vueuse/core";
import {reactive, ref} from "vue";
import useAlerts from "@/store/useAlerts.js";
import {usePage} from "@inertiajs/vue3";

const source = ref('')
const { text, copy, copied, isSupported } = useClipboard({ source })
const {addAlert, alerts} = useAlerts();

const props = defineProps({
  copyText: String,
});
const copyText = (text) => {
  copy(text);
  //add alert
  addAlert({ message: 'Copié dans le presse-papier', type: 'info' });
}
</script>

<template>
  <button @click="copyText(copyText)" class="text-gray-300">
    <Icon icon="solar:share-line-duotone" class="w-6 h-6" />
  </button>
</template>

<style scoped>

</style>
