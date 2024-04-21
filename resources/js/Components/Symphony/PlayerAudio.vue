<template>
  <div class="relative">
    <audio ref="audioPlayer" @timeupdate="updateProgress">
      <source :src="'/storage/' + song.path">
    </audio>
    <div>
      <svg
          width="160"
          height="160"
          viewBox="0 0 160 160"
          style="transform: rotate(-90deg);cursor: pointer;"
          @mousedown="startDrag"
          @mousemove="dragging && handleDrag"
          @mouseup="endDrag"
          @mouseleave="endDrag"
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
            stroke="#60e6a8"
            stroke-width="12px"
            :stroke-dasharray="circumference"
            :stroke-dashoffset="circumference * ((100 - percent) / 100) + 'px'"
        ></circle>
      </svg>
    </div>
    <button class="play-pause-button" @click="togglePlayback">
      <svg v-if="!isPlaying" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
        <path d="M8 5v14l11-7z" />
      </svg>
      <svg v-else xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
        <path d="M6 6h4v12H6zm8 0h4v12h-4z" />
      </svg>
    </button>
  </div>
</template>

<script setup>
import { defineProps, ref } from 'vue';

const props = defineProps({
  song: Object,
});

const audioPlayer = ref(null);
const isPlaying = ref(false);
const circumference = ref(439.6);
const percent = ref(0);
const draggable = ref(null);
let dragging = false;
let offsetX = 0;
let offsetY = 0;

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

function startDrag(event) {
  dragging = true;
  console.log(audioPlayer.value.currentTime);
  // Get the position of the mouse relative to the center of the circle
  const rect = event.target.getBoundingClientRect();
  const mouseX = event.clientX - rect.left - rect.width / 2;
  const mouseY = event.clientY - rect.top - rect.height / 2;

  // Calculate the angle of the mouse position relative to the center of the circle
  const angle = Math.atan2(mouseY, mouseX);

  // Convert the angle to degrees
  let degrees = angle * (180 / Math.PI) + 90;
  if (degrees < 0) {
    degrees += 360;
  }

  // Calculate the percentage of the song based on the angle
  const percentage = degrees / 360;

  // Seek the audio to the corresponding position
  const duration = audioPlayer.value.duration;
  const currentTime = duration * percentage;

  // Now 'currentTime' holds the time in seconds corresponding to the percentage
  console.log("Current time in seconds:", currentTime);
  // Update your audio player's current time
  audioPlayer.value.duration = currentTime;
  }

function handleDrag(event) {
  if (!dragging) return;
  const x = event.clientX - offsetX;
  const y = event.clientY - offsetY;
  draggable.value.setAttribute('cx', x);
  draggable.value.setAttribute('cy', y);
}

function endDrag() {
  dragging = false;
}

const seek = (event) => {
  const boundingRect = event.currentTarget.getBoundingClientRect();
  const clickX = event.clientX - boundingRect.left;
  const percentage = clickX / boundingRect.width;
  const duration = audioPlayer.value.duration;
  audioPlayer.value.currentTime = duration * percentage;
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
  color: #00bcd4; /* Change color as needed */
}
</style>
