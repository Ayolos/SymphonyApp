<script setup>
import SymphonyLayout from "@/Layouts/SymphonyLayout.vue";
import PostInfo from "@/Components/Symphony/PostInfo.vue";
import {Link, useForm} from "@inertiajs/vue3";
import Post from "@/Components/Symphony/Post/Post.vue";
import {Icon} from "@iconify/vue";
import {ref} from "vue";
import Modal from "@/Components/Symphony/Modal/Modal.vue";
import PlayerAudio from "@/Components/Symphony/PlayerAudio.vue";
import MainModal from "@/Components/Symphony/Modal/MainModal.vue";
import { useClipboard } from '@vueuse/core'
import ShareButton from "@/Components/Symphony/Button/ShareButton.vue";

defineProps({
    post: Object,
});

const like = ref(false);

const source = ref('')
const { text, copy, copied, isSupported } = useClipboard({ source })


const formComment = useForm({
    content: "",
    post_id: null,
});

const formReply = useForm({
    content: "",
    parent_id: null,
    post_id: null,
});

const submitCommentReply = (commentId, postId) => {
  formReply.parent_id = commentId;
  formReply.post_id = postId;
  formReply.post(route('comments.reply'), {
    preserveScroll: true,
    onSuccess: () => {
      formReply.reset('content');
    }
  });
};

const submitComment = (postId) => {
  console.log("response");
  formComment.post_id = postId;
  formComment.post(route('comments.store'), {
    preserveScroll: true,
    onSuccess: () => {
      formComment.reset('content');
    }
  });
};
</script>

<template>
    <SymphonyLayout>
        <div class="">
            <Post :src="post.user.profile_photo_url" :connectLine="false" :createdAt="post.created_at">
                <template #name>
                    {{ post.user.name }}
                </template>
                <template #at>
                    @{{ post.user.username }}
                </template>
                <template #content>
                    {{ post.content }}
                </template>
              <template #media>
                <PlayerAudio :song="post.song"></PlayerAudio>
              </template>
                <template #likeButton>
                    <div class="flex flex-row gap-2 items-center">
                        <Link as="button" method="post" :href="post.isLiked ? route('posts.unlike', { post: post.id }) : route('posts.like', { post: post.id })" >
                            <Icon icon="iconamoon:heart-duotone" class="w-6 h-6 transition hover:scale-110 hover:rotate-6 ease-in-out" :class="[ post.isLiked ? 'text-secondary-500' : 'text-gray-300']" />
                        </Link>
                        <h1 class="text-md text-symph-200 font-bold">{{ post.nbLikes }}</h1>
                    </div>
                    <div class="flex flex-row gap-2 items-center">
                      <MainModal @submit="submitComment(post.id)">
                        <template #button>
                          <Icon icon="iconamoon:comment-duotone" class="w-5 h-5 transition hover:scale-110 ease-in-out text-gray-300" />
                        </template>
                        <template #modalTitle>
                          Ajouter un commentaire
                        </template>
                        <template #content>
                          <div class="flex flex-col gap-3 px-4 justify-end">
                            <textarea required v-model="formComment.content" placeholder="Ecrit ton commentaire" class="w-full text-symph-200 h-48 rounded-lg bg-symph-800 border-symph-500 resize-none"></textarea>
                            <button class="bg-secondary-500 text-white rounded-lg px-4 py-2">Envoyer</button>
                          </div>
                        </template>
                      </MainModal>
                      <h1 class="text-md text-symph-200 font-bold">{{ post.nbComments }}</h1>
                    </div>
                    <div class="flex flex-row gap-2 items-center">
                      <ShareButton :copy-text="route('posts.show', {id: post.id})"></ShareButton>
                    </div>
                </template>
            </Post>
            <div v-for="(comment, index) in post.comments" :key="comment.id" class="pl-10 mt-3 flex flex-col w-full items-center relative">
              <div v-if="index !== post.comments.length - 1" class="absolute w-0.5 h-full top-0 bg-symph-400 left-5"></div>
              <div class="bg-symph-100 rounded-lg w-full">
                    <Post :src="comment.user.profile_photo_url" :created-at="comment.created_at">
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
                                    <Icon icon="iconamoon:heart-duotone" class="w-6 h-6 transition hover:scale-110 hover:rotate-6 ease-in-out" :class="[ comment.isLiked ? 'text-secondary-500' : 'text-gray-300']" />
                                </Link>
                                <h1 class="text-md text-symph-200 font-bold">{{ comment.nbLikes }}</h1>
                            </div>
                            <div class="flex flex-row gap-2 items-center">
                              <MainModal @submit="submitCommentReply(comment.id, post.id)" @open="formReply.content = '@' + comment.user.username + ' '">
                                <template #button>
                                  <Icon icon="iconamoon:comment-duotone" class="w-5 h-5 transition hover:scale-110 ease-in-out text-gray-300" />
                                </template>
                                <template #modalTitle>
                                  Ajouter un commentaire
                                </template>
                                <template #content>
                                  <div class="flex flex-col gap-3 px-4 justify-end">
                                    <textarea required v-model="formReply.content" placeholder="Ecrit ton commentaire" class="w-full text-symph-200 h-48 rounded-lg bg-symph-800 border-symph-500 resize-none"></textarea>
                                    <button class="bg-secondary-500 text-white rounded-lg px-4 py-2">Envoyer</button>
                                  </div>
                                </template>
                              </MainModal>
                              <h1 class="text-md text-symph-200 font-bold">{{ comment.nbReplies }}</h1>
                            </div>
                        </template>
                    </Post>
                </div> <!-- Comment Content -->
                <div v-for="(reply, index) in comment.reply" :key="reply.id" class="pl-10 flex w-full items-center relative">
                  <div v-if="index !== comment.reply.length - 1" class="absolute w-0.5 h-full top-0 bg-symph-400 left-5"></div>
                  <div class="bg-symph-100 rounded-lg my-3 w-full">
                        <Post :src="reply.user.profile_photo_url" :created-at="reply.created_at">
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
                                        <Icon icon="iconamoon:heart-duotone" class="w-6 h-6 transition hover:scale-110 hover:rotate-6 ease-in-out" :class="[ reply.isLiked ? 'text-secondary-500' : 'text-gray-300']" />
                                    </Link>
                                    <h1 class="text-md text-symph-200 font-bold">{{ reply.nbLikes }}</h1>
                                </div>
                              <div class="flex flex-row gap-2 items-center">
                                <MainModal @submit="submitCommentReply(comment.id, post.id)" @open="formReply.content = '@' + reply.user.username + ' '">
                                  <template #button>
                                    <Icon icon="iconamoon:comment-duotone" class="w-5 h-5 transition hover:scale-110 ease-in-out text-gray-300" />
                                  </template>
                                  <template #modalTitle>
                                    Ajouter un commentaire
                                  </template>
                                  <template #content>
                                    <div class="flex flex-col gap-3 px-4 justify-end">
                                      <textarea required v-model="formReply.content" placeholder="Ecrit ton commentaire" class="w-full text-symph-200 h-48 rounded-lg bg-symph-800 border-symph-500 resize-none"></textarea>
                                      <button class="bg-secondary-500 text-white rounded-lg px-4 py-2">Envoyer</button>
                                    </div>
                                  </template>
                                </MainModal>
                              </div>
                            </template>
                        </Post>
                    </div> <!-- Comment Content -->
                </div>
            </div>
        </div>
    </SymphonyLayout>
</template>
