<script setup>
import SymphonyLayout from "@/Layouts/SymphonyLayout.vue";
import PostInfo from "@/Components/Symphony/PostInfo.vue";
import {useForm} from "@inertiajs/vue3";

defineProps({
    post: Object,
});

const commentForm = useForm({
    content: "",
    post_id: null,
});

const submitComment = (post) => {
    commentForm.post_id = post.id;
    commentForm.post(route('comments.store'), {
        onSuccess: () => {
            commentForm.reset('content');
        }
    });
};

</script>

<template>
    <SymphonyLayout>
        <div class="p-10">
            <!-- Render the post -->
            <div class="bg-white rounded-lg shadow p-4 mb-4">
                <PostInfo :src="post.user.profile_photo_url">
                    <template #name>
                        {{ post.user.name }}
                    </template>
                    <template #at>
                        @{{ post.user.username }}
                    </template>
                </PostInfo>
                <p class="text-gray-500">{{ post.content }}</p>
            </div>

            <!-- Render comments in chronological order -->
            <div v-for="comment in post.comments" :key="comment.id" class="bg-gray-100 rounded-lg shadow p-4 mb-4">
                <PostInfo :src="comment.user.profile_photo_url">
                    <template #name>
                        {{ comment.user.name }}
                    </template>
                    <template #at>
                        @{{ comment.user.username }}
                    </template>
                </PostInfo>
                <p class="text-gray-500">{{ comment.content }}</p>
            </div>
        </div>
        <div class="px-10">
            <form @submit.prevent="submitComment(post)">
                <h1 class="text-xl font-bold">Commenter</h1>
                <textarea class="w-full rounded-lg" v-model="commentForm.content"></textarea>
                <button type="submit" class="bg-secondary-500 text-white rounded-lg px-4 py-2">Envoyer</button>
            </form>
        </div>
    </SymphonyLayout>
</template>
