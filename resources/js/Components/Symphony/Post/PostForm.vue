<script setup>
import {useForm} from "@inertiajs/vue3";
import {ref, watch} from "vue";
import MainModal from "@/Components/Symphony/Modal/MainModal.vue";
import {Icon} from "@iconify/vue";
import gsap from 'gsap';
import CounterMessage from "@/Components/Symphony/CounterMessage.vue";
import Alerts from "@/Components/Symphony/Alerts.vue";
import UserInfo from "@/Components/Symphony/Post/UserInfo.vue";

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

const nbrCharacters = ref(0);
const error = ref(null);
const isPosting = ref(false);

const submitPost = () => {
  formPost.post(route('posts.store'), {
    forceFormData: true,
    onSuccess: () => {
      isPosting.value = true;
      setTimeout(() => {
        isPosting.value = false;
      }, 2000);
      formPost.reset('content');
      resetSong();
    },
    onError: () => {
      error.value = 'Erreur ! Veuillez réessayer en ajoutant une musique.';
      setTimeout(() => {
        error.value = null
      }, 2000);
    }
  });
};

const audioPreview = ref('');

watch(() => formPost.content, () => {
  nbrCharacters.value = formPost.content.length;

});
const onFileChange = (e) => {
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

const resetSong = () => {
  formPost.file = null;
  audioPreview.value = null;
  document.getElementById('fileInput').value = null;
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
  <form class="w-full rounded-2xl border border-symph-500" @submit.prevent="submitPost">
      <div class="flex flex-row items-start h-56">
        <div class="flex-col items-center gap-4 p-4 w-full h-full">
          <div class="flex flex-row gap-4 p-4">
            <img :src="$page.props.auth.user.profile_photo_url" class="w-12 h-12 rounded">
            <UserInfo :user-id="$page.props.auth.user.id" :name="$page.props.auth.user.name" :username="$page.props.auth.user.username"></UserInfo>
          </div>
          <div class="pl-4 flex h-28 flex-col justify-between items-end gap-2">
            <textarea v-model="formPost.content"
                      class="w-full h-full focus:ring-0 resize-none text-symph-100 border-0 p-0 bg-symph-800"
                      placeholder="Exprimez-vous..."
                      maxlength="120"
                      required></textarea>
            <div class="flex flex-row gap-4 items-center">
              <CounterMessage class="text-symph-100" :message="formPost.content" :maxCharacters="120"></CounterMessage>
              <button type="submit" class="border-secondary border hover:bg-secondary/40 bg-secondary/50 text-white rounded-lg px-4 py-2">Publier</button>
            </div>
          </div>
        </div>
        <div class="flex flex-col gap-3 justify-center items-center aspect-square p-5 h-full bg-symph-700 rounded-r-2xl">
          <div class="relative bg-secondary p-4 rounded-lg hover:scale-110 shadow-secondary shadow-2xl hover:transition">
            <input id="fileInput" class="absolute inset-0 opacity-0 z-10" type="file" accept=".mp3" @change="onFileChange" required>
            <Icon class="text-white" icon="mingcute:music-3-line" width="50"></Icon>
          </div>
          <div class="">
            <div class="flex flex-row items-center gap-4">
              <div v-if="audioPreview">
                <div class="flex flex-row gap-4">
                  <div class="rounded-full p-0.5 bg-green-500 w-max flex justify-center items-center">
                    <Icon icon="material-symbols:check" width="20" class="text-white"></Icon>
                  </div>
                  <button type="reset" @click.prevent="resetSong">
                    <Icon icon="system-uicons:reset" width="20" class="text-white transition hover:scale-110 ease-in-out hover:-rotate-90"></Icon>
                  </button>
                </div>
              </div>
              <div v-else>
                <div class="rounded-full p-0.5 bg-red-500 w-max flex justify-center items-center">
                  <Icon icon="ic:round-close" width="20" class="text-white"></Icon>
                </div>
              </div>
              <div v-if="audioPreview" class="h-full flex justify-center">
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
  </form>
</template>

<style scoped>

</style>
