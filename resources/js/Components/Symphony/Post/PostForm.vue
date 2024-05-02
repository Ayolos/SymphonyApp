<script setup>
import {useForm} from "@inertiajs/vue3";
import {ref} from "vue";
import MainModal from "@/Components/Symphony/Modal/MainModal.vue";
import {Icon} from "@iconify/vue";

const formPost = useForm({
  content: '',
  title: '',
  artist: '',
  genre: '',
  album: '',
  release_date: '',
  duration: '',
  file: null,
});
const submitPost = () => {
  formPost.post(route('posts.store'), {
    forceFormData: true,
    onSuccess: () => {
      formPost.reset('content');
    }
  });
};

const audioPreview = ref('');

const onFileChange = (e) => {
  audioSubmit.value = true
  formPost.file = e.target.files[0];
  if (formPost.file) {
    const reader = new FileReader();
    reader.onload = (event) => {
      audioPreview.value = event.target.result;
    };
    reader.readAsDataURL(formPost.file);
  } else {
    audioPreview.value = '';
  }
};

const audioSubmit = ref(false)
const resetSong = () => {
  formPost.file = null;
  audioPreview.value = '';
  audioSubmit.value = false;
};

const audioPlayer = ref(null);
const isPlaying = ref(false);

const togglePlayPause = () => {
  if (audioPlayer.value.paused) {
    audioPlayer.value.play();
    isPlaying.value = true;
  } else {
    audioPlayer.value.pause();
    isPlaying.value = false;
  }
};

</script>

<template>
  <form class="w-full" @submit.prevent="submitPost">
    <div class="bg-symph-100 rounded-lg">
      <div class="flex flex-row justify-between items-start h-48">
        <div class=" basis-2/3 flex-col items-center gap-4 p-4 w-full h-full">
          <div class="flex flex-row items-center gap-4 p-4">
            <img :src="$page.props.auth.user.profile_photo_url" class="w-12 h-12 rounded">
            <div class="flex-col flex">
              <h1 class="text-gray-400 truncate text-nowrap">{{ $page.props.auth.user.name }}</h1>
              <span class="text-gray-500 text-sm">@{{ $page.props.auth.user.username }}</span>
            </div>
          </div>
          <div class="pl-4 flex flex-row justify-between items-end gap-8">
            <textarea v-model="formPost.content"
                      class="w-full h-20 focus:ring-0 resize-none border-0 p-0 rounded-lg bg-symph-100"
                      placeholder="Exprimez-vous..."
                      required></textarea>
            <div>
              <button type="submit" class="bg-symph-500 text-white rounded-lg px-4 py-2">Publier</button>
            </div>
          </div>
        </div>
        <div class="flex flex-col gap-3 justify-center items-center aspect-square p-5 h-full bg-symph-700 rounded-r-md">
          <div class="relative bg-secondary p-4 rounded-lg hover:scale-110 hover:transition">
            <input id="fileInput" class="absolute inset-0 opacity-0 z-10" type="file" accept=".mp3" @change="onFileChange" required>
            <Icon class="text-white" icon="uil:music" width="50"></Icon>
          </div>
          <div class="">
            <div class="flex flex-row items-center gap-4">
              <div v-if="audioSubmit">
                <div class="rounded-full p-0.5 bg-green-500 w-max flex justify-center items-center">
                  <Icon icon="material-symbols:check" width="20" class="text-white"></Icon>
                </div>
              </div>
              <div v-else>
                <div class="rounded-full p-0.5 bg-red-500 w-max flex justify-center items-center">
                  <Icon icon="ic:round-close" width="20" class="text-white"></Icon>
                </div>
              </div>
              <button type="reset" @click="resetSong">
                <Icon icon="system-uicons:reset" width="20" class="text-white"></Icon>
              </button>
              <div v-if="audioPreview && audioSubmit" class="h-full flex justify-center">
                <audio :src="audioPreview" class="h-20 rounded-lg" ref="audioPlayer"></audio>
                <button @click="togglePlayPause" type="button" class="text-white">
                  <svg v-if="!isPlaying" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                    <path d="M8 5v14l11-7z" />
                  </svg>
                  <svg v-else xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                    <path d="M6 6h4v12H6zm8 0h4v12h-4z" />
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</template>

<style scoped>

</style>
