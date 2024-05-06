<template>
  <div class="relative">
    <audio ref="audioPlayer" id="stream" @timeupdate="updateProgress" preload>
      <source :src="'/storage/' + song.path">
    </audio>
    <div>
      <svg
          width="140"
          height="140"
          viewBox="0 0 160 160"
          style="transform: rotate(-90deg);cursor: pointer;"
      >
        <circle
            r="70"
            cx="80"
            cy="80"
            fill="transparent"
            stroke="#e0e0e0"
            stroke-width="12px"
        ></circle>
        <circle
            ref="draggable"
            r="70"
            cx="80"
            cy="80"
            fill="transparent"
            stroke="#FFA000"
            stroke-width="12px"
            stroke-linecap="round"
            :stroke-dasharray="circumference"
            :stroke-dashoffset="circumference * ((100 - percent) / 100) + 'px'"
        ></circle>
      </svg>
    </div>
    <button class="play-pause-button" @click.prevent="togglePlayback">
      <Icon v-if="!isPlaying" icon="carbon:play-filled" class="w-8 h-8 text-secondary" />
      <Icon v-else icon="carbon:pause-filled" class="w-8 h-8" />
    </button>
  </div>
  <div></div>
</template>

<script setup>
import {computed, defineProps, onUpdated, ref} from 'vue';
import {Icon} from "@iconify/vue";

const props = defineProps({
  song: Object,
});

const audioPlayer = ref(null);
const isPlaying = ref(false);
const circumference = ref(439.6);
const percent = ref(0);
const draggable = ref(null);


const togglePlayback = () => {
  if (audioPlayer.value.paused) {
    audioPlayer.value.play();
    isPlaying.value = true;
  } else {
    audioPlayer.value.pause();
    isPlaying.value = false;
  }
};


const updateProgress = () => {
  const currentTime = audioPlayer.value.currentTime;
  const duration = audioPlayer.value.duration;
  percent.value = (currentTime / duration) * 100;
};
</script>

<style scoped>
.play-pause-button {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background: none;
  border: none;
  cursor: pointer;
  outline: none;
  font-size: 24px;
  color: #FFA000; /* Change color as needed */
}
</style>
