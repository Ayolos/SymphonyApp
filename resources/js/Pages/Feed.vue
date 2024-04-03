<template>
    <Head title="Feed" />
    <SymphonyLayout :isLogin="canLogin">
        <template #trendingUsers>
            <h1 class="text-white font-bold text-xl pb-5">Vous aimeriez aussi...</h1>
            <div v-for="trendingUser in trendingUsers" class="flex flex-row gap-4 justify-between">
                <div class="flex flex-row items-center pb-4 gap-4">
                    <img :src=trendingUsers.profile_photo_url class="w-12 h-12 rounded">
                    <div class="flex-col flex">
                        <span class="text-gray-400 truncate text-nowrap">{{trendingUser.name}}</span>
                        <span class="text-gray-500 text-sm">@{{trendingUser.username}}</span>
                    </div>
                </div>
                <div class="flex flex-row items-center gap-4 pr-4">
                    <form @submit.prevent="follow(trendingUser)">
                        <button class="bg-symph-500 text-white rounded-lg px-4 py-2">Suivre</button>
                    </form>
                </div>
            </div>
        </template>
        <template #postForm>
            <form @submit.prevent="submitPost" class="w-full">
                <div class="bg-symph-100 rounded-lg">
                    <div class="flex flex-row justify-between items-start h-48">
                        <div class="flex-col items-center gap-4 p-4 w-full h-full">
                            <div class="flex flex-row items-center gap-4 p-4">
                                <img :src="$page.props.auth.user.profile_photo_url" class="w-12 h-12 rounded">
                                <div class="flex-col flex">
                                    <h1 class="text-gray-400 truncate text-nowrap">{{ $page.props.auth.user.name }}</h1>
                                    <span class="text-gray-500 text-sm">@{{ $page.props.auth.user.username }}</span>
                                </div>
                            </div>
                            <div class="pl-4 pr-20 flex flex-row justify-between items-end gap-8">
                                <!-- Définir la taille du textarea -->
                                <textarea required v-model="formPost.content" class="w-full h-20 focus:ring-0 resize-none border-0 p-0 rounded-lg bg-symph-100" placeholder="Exprimez-vous..."></textarea>
                                <div>
                                    <button class="bg-symph-500 text-white rounded-lg px-4 py-2">Publier</button>
                                </div>
                            </div>
                        </div>
                        <img @click="openSongModal" src="https://img.icons8.com/ios/452/music--v1.png" class="w-max h-full aspect-square rounded-lg">
                        <div>
                            <h1>title: {{ submitFormSong.title }}</h1>
                            <h1>artist: {{ submitFormSong.artist }}</h1>
                            <h1>genre: {{ submitFormSong.genre }}</h1>
                            <h1>album: {{ submitFormSong.album }}</h1>
                            <h1>release date: {{ submitFormSong.release_date }}</h1>
                            <h1>duration: {{ submitFormSong.duration }}</h1>
                            <h1>file: {{ submitFormSong.file }}</h1>
                        </div>
                    </div>
                </div>
            </form>
        </template>
        <div v-for="post in posts" :key="post.id" class="pb-10">
            <Post :href="route('posts.show', {id: post.id})"
                  :src="post.user.profile_photo_url">
                <template #name>
                    {{ post.user.name }}
                </template>
                <template #at>
                    @{{ post.user.username }}
                </template>
                <template #content>
                    {{ post.content }}
                </template>
                <template #likeButton>
                    <div class="flex flex-row gap-2 items-center">
                        <Link as="button" method="post" :href="post.isLiked ? route('posts.unlike', { post: post.id }) : route('posts.like', { post: post.id })" >
                            <Icon icon="iconamoon:heart-duotone" class="w-6 h-6" :class="[ post.isLiked ? 'text-secondary-500' : 'text-gray-300']" />
                        </Link>
                        <h1 class="text-md text-symph-200 font-bold">{{ post.nbLikes }}</h1>
                    </div>
                    <div class="flex flex-row gap-2 items-center">
                        <button @click="openModal(post)" class="text-gray-300">
                            <Icon icon="iconamoon:comment-duotone" class="w-5 h-5" />
                        </button>
                        <h1 class="text-md text-symph-200 font-bold">{{ post.nbComments }}</h1>
                    </div>
                </template>
            </Post>
            <div v-for="(comment, index) in post.comments" :key="comment.id" class="pl-10 pt-3 flex flex-col w-full items-center relative">
                <div class="bg-symph-100 rounded-lg w-full">
                    <div v-if="index !== post.comments.length - 1" class="absolute w-0.5 h-full top-0 bg-symph-400 left-5"></div>
                    <Post :src="comment.user.profile_photo_url">
                        <template #connectLine>
                            <div class="absolute border-b-2 border-l-2 border-symph-300 rounded-bl-xl h-full w-5 left-5"></div>
                        </template>
                        <template #name>
                            {{ comment.user.name }}
                        </template>
                        <template #at>
                            @{{ comment.user.username }}
                        </template>
                        <template #content>
                            {{ comment.content }}
                        </template>
                        <template #likeButton>
                            <div class="flex flex-row gap-2 items-center">
                                <Link as="button" method="post" :href="comment.isLiked ? route('comments.unlike', { comment: comment.id }) : route('comments.like', { comment: comment.id })" >
                                    <Icon icon="iconamoon:heart-duotone" class="w-6 h-6" :class="[ comment.isLiked ? 'text-secondary-500' : 'text-gray-300']" />
                                </Link>
                                <h1 class="text-md text-symph-200 font-bold">{{ comment.nbLikes }}</h1>
                            </div>
                            <div class="flex flex-row gap-2 items-center">
                                <button @click="openCommentModal(comment, post)" class="text-gray-300">
                                    <Icon icon="iconamoon:comment-duotone" class="w-5 h-5" />
                                </button>
                                <h1 class="text-md text-symph-200 font-bold">{{ comment.nbReplies }}</h1>
                            </div>
                        </template>
                    </Post>
                </div>
                <div v-for="(reply, index) in comment.reply" :key="reply.id" class="px-10 flex flex-col w-full items-center relative">
                    <div class="bg-symph-100 rounded-lg my-3 w-full">
                        <div v-if="index !== comment.reply.length - 1" class="absolute w-0.5 h-full top-0 bg-symph-400 left-5"></div>
                        <Post :src="reply.user.profile_photo_url">
                            <template #name>
                                {{ reply.user.name }}
                            </template>
                            <template #at>
                                @{{ reply.user.username }}
                            </template>
                            <template #content>
                                {{ reply.content }}
                            </template>
                            <template #likeButton>
                                <div class="flex flex-row gap-2 items-center">
                                    <Link as="button" method="post" :href="reply.isLiked ? route('comments.unlike', { comment: reply.id }) : route('comments.like', { comment: reply.id })" >
                                        <Icon icon="iconamoon:heart-duotone" class="w-6 h-6" :class="[ reply.isLiked ? 'text-secondary-500' : 'text-gray-300']" />
                                    </Link>
                                    <h1 class="text-md text-symph-200 font-bold">{{ reply.nbLikes }}</h1>
                                </div>
                                <div class="flex flex-row gap-2 items-center">
                                    <button @click="openReplyModal(comment, post)" class="text-gray-300">
                                        <Icon icon="iconamoon:comment-duotone" class="w-5 h-5" />
                                    </button>
                                </div>
                            </template>
                        </Post>
                    </div> <!-- Comment Content -->
                </div>
            </div>
        </div>

        <Modal v-if="isSongModalOpen" @close="closeModalSong">
            <template #content>
                <form @submit.prevent="submitSong" enctype="multipart/form-data">
                    <h1>Hello</h1>
                    <input type="text" placeholder="title" v-model="formPost.title">
                    <input type="text" placeholder="artist" v-model="formPost.artist">
                    <input type="text" placeholder="genre" v-model="formPost.genre">
                    <input type="text" placeholder="album name" v-model="formPost.album">
                    <input type="text" placeholder="release date" v-model="formPost.release_date">
                    <input type="text" placeholder="duration" v-model="formPost.duration">
                    <input type="file" placeholder="file" @change="onFileChange">
                    <audio controls :src="audioPreview"></audio>
                    <button>Submit</button>
                </form>
            </template>
        </Modal>

        <Modal v-if="isReplyModalOpen" @close="closeModalReply">
            <template #content>
                <form @submit.prevent="submitReply">
                    <h1>envoyer un reply</h1>
                    <div class="my-5">
                        <textarea required v-model="formReply.content" class="w-full rounded-lg"></textarea>
                    </div>
                    <div class="flex justify-end">
                        <button class="bg-secondary-500 text-white rounded-lg px-4 py-2">Envoyer</button>
                    </div>
                </form>
            </template>
        </Modal>

        <Modal v-if="isCommentModalOpen" @close="closeModalComment">
            <template #content>
                <form @submit.prevent="submitComment">
                    <div class="my-5">
                        <textarea required v-model="formComment.content" class="w-full rounded-lg"></textarea>
                    </div>
                    <div class="flex justify-end">
                        <button class="bg-secondary-500 text-white rounded-lg px-4 py-2">Envoyer</button>
                    </div>
                </form>
            </template>
        </Modal>

        <!-- Modal -->
        <Modal v-if="isModalOpen" @close="closeModal">
            <template #content>
                <form @submit.prevent="submitComment">
                    <div class="my-5">
                        <textarea required v-model="formComment.content" class="w-full rounded-lg"></textarea>
                    </div>
                    <div class="flex justify-end">
                        <button class="bg-secondary-500 text-white rounded-lg px-4 py-2">Envoyer</button>
                    </div>
                </form>
            </template>
        </Modal>
    </SymphonyLayout>
</template>

<script setup>
import {Head, router, useForm, Link} from '@inertiajs/vue3';
import SymphonyLayout from "@/Layouts/SymphonyLayout.vue";
import {computed, onMounted, reactive, ref} from "vue";
import { Icon } from "@iconify/vue";
import Modal from "@/Components/Symphony/Modal/Modal.vue";
import PostInfo from "@/Components/Symphony/PostInfo.vue";
import Post from "@/Components/Symphony/Post/Post.vue";

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
    posts: Array,
    trendingUsers: Array,
});

const like = ref(false);

const formPost = useForm({
    content: '',
        title: '',
        artist: '',
        genre: '',
        album: '',
        release_date: '',
        duration: '',
        file: '',
});

const formComment = useForm({
    content: "",
    post_id: null,
    parent_id: null,
});

const formReply = useForm({
    content: "",
    parent_id: null,
    post_id: null,
});

const submitFormSong = ref({
    title: '',
    artist: '',
    genre: '',
    album: '',
    release_date: '',
    duration: '',
    file: '',
    post_id: null,
});

const isModalOpen = ref(false);
const selectedPost = ref(null);

const selectedComment = ref(null);
const isReplyModalOpen = ref(false);
const isCommentModalOpen = ref(false);

const isSongModalOpen = ref(false);

const songSubmit = ref(false);

const audioPreview = ref('');

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
const openSongModal = () => {
    //songSubmit.value = false;
    isSongModalOpen.value = true;
};


const openModal = (post) => {
    selectedPost.value = post;
    isModalOpen.value = true;
    formComment.post_id = post.id;
};

const openCommentModal = (comment, post) => {
    selectedComment.value = comment;
    isCommentModalOpen.value = true;
    formComment.parent_id = comment.id;
    formComment.post_id = post.id;
};

const openReplyModal = (comment, post) => {
    selectedComment.value = comment;
    isReplyModalOpen.value = true;
    formReply.content = '@' + comment.user.username + ' '
    formReply.parent_id = comment.id;
    formReply.post_id = post.id;
};
const closeModal = () => {
    isModalOpen.value = false;
};

const closeModalSong = () => {
    isSongModalOpen.value = false;
};

const closeModalReply = () => {
    isReplyModalOpen.value = false;
};

const closeModalComment = () => {
    isCommentModalOpen.value = false;
};

const submitPost = () => {
    console.log(formPost.file);
    formPost.post(route('posts.store'), {
        forceFormData: true,
        onSuccess: () => {
            formPost.reset('content');
        }
    });
};

const submitSong = () => {
    submitFormSong.value.title = formPost.title;
    submitFormSong.value.artist = formPost.artist;
    submitFormSong.value.genre = formPost.genre;
    submitFormSong.value.album = formPost.album;
    submitFormSong.value.release_date = formPost.release_date;
    submitFormSong.value.duration = formPost.duration;
    submitFormSong.value.file = formPost.file;
    closeModalSong()
    /*formSong.post(route('song.store'), {
        onSuccess: () => {
            formSong.reset('title', 'artist', 'genre', 'album', 'release_date', 'duration', 'file');
        }
    });*/
};
const submitReply = () => {
    closeModalReply();
    formReply.post(route('comments.reply'), {
        preserveScroll: true,
        onSuccess: () => {
            formReply.reset('content');
        }
    });
};
const submitComment = () => {
    closeModalComment();
    formComment.post(route('comments.reply'), {
        preserveScroll: true,
        onSuccess: () => {
            formComment.reset('content');
        }
    });
};

const userTrendingFormPost = useForm({
    trendingUser: ''
})

const follow = (user) => {
  userTrendingFormPost.trendingUser = user.id;
  userTrendingFormPost.post(route('user.follow'));
}


</script>


<style>
/* Styles pour votre modal */
</style>
