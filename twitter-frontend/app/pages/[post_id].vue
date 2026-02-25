<template>
    <div class="flex-container">
        <SideNav :user="user" :refresh="refresh" />
        <div class="main-container">
            <div class="title">コメント</div>
            <div class="tmp-message" v-if="!post">Loading ...</div>
            <Message v-else :user="user" :post="post" :refresh="refresh" :isIndex="false"/>
            <div class="sub-title">コメント</div>
            <div class="comment-container" v-for="comment in comments">
                <div class="item-name">{{ comment.user.name }}</div>
                <div class="item-content">{{ comment.content }}</div>
            </div>
            <form @submit.prevent="handleCreateComment">
                <input type="text" v-model="content">
                <div class="form-error">{{ errors.content }}</div>
                <div class="right-btn">
                    <button type="submit" v-bind:disabled="isLoadingCreateComment">
                        {{ isLoadingCreateComment ? 'コメント中...' : 'コメント' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup lang="ts">
import { useField, useForm } from 'vee-validate';
import * as yup from 'yup';
import auth from '../middleware/auth';

definePageMeta({
    middleware: auth,
});

// バリデーションルール定義
const schema = yup.object({
    content: yup.string().required().max(120),
});
const { errors, resetForm } = useForm({
    validationSchema: schema
});
const { value: content } = useField<string>('content');

const { user } = useFirebaseAuth();
const { fetchAPI } = useApi();
const route = useRoute();
const isLoadingCreateComment = ref(false);

// 投稿情報の取得
const { data: fetchedData, refresh, pending } = await useAsyncData(
    'post',
    async () => {
        const token = await user.value?.getIdToken();
        const resPost = await fetchAPI(`/posts/${route.params.post_id}`, {
            method: 'GET',
            headers: {
                Authorization: `Bearer ${token}`,
            },
        }) as { post: any; message: string };
        console.log("[INFO] ", resPost.message);
        const resComments = await fetchAPI(`/posts/${route.params.post_id}/comment`, {
            method: 'GET',
            headers: {
                Authorization: `Bearer ${token}`,
            },
        }) as { comments: any; message: string };
        console.log("[INFO] ", resComments.message);
        return { resPost, resComments };
    },
    {
        watch: [user],  // userの状態が変わるたびに投稿情報を再取得
    }
)
const post = computed(() => fetchedData.value?.resPost.post || null);
const comments = computed(() => fetchedData.value?.resComments.comments || null);

// コメント登録処理
const handleCreateComment = async () => {
    isLoadingCreateComment.value = true;

    // Laravelのcommentsテーブルに投稿を登録
    try {
        const token = await user.value?.getIdToken();
        const res = await fetchAPI(`/posts/${route.params.post_id}/comment`, {
            method: 'POST',
            headers: {
                Authorization: `Bearer ${token}`,
            },
            body: {
                content: content.value,
            }
        }) as { message: string };
        console.log("[INFO] ", res.message);
        resetForm();
        refresh(); // コメント成功時はホーム画面をリロードして新しいコメントを表示
    } catch (err) {
        console.error("[ERROR] Fail to store comment:", err);
    }

    isLoadingCreateComment.value = false;
};
</script>

<style scoped>
.flex-container {
    display: flex;
    justify-content: space-between;
}

.main-container {
    width: 80%;
    color: white;
}

.main-container .tmp-message {
    padding: 10px;
    border-left: 1px solid white;
    border-bottom: 1px solid white;
}

.main-container .title {
    padding: 10px;
    border-left: 1px solid white;
    border-bottom: 1px solid white;
    font-size: 20px;
}

.main-container .sub-title {
    padding: 5px;
    border-left: 1px solid white;
    border-bottom: 1px solid white;
    text-align: center;
}

.comment-container {
    padding: 10px;
    border-left: 1px solid white;
    border-bottom: 1px solid white;
}

.comment-container .item-content {
    font-size: 10px;
}

form {
    margin-left: 10px;
    margin-top: 20px;
}

form input {
    display: block;
    width: 90%;
    padding: 10px;
    background-color: #15202b;
    border: 1px solid white;
    border-radius: 5px;
    color: white;
    resize: none;
}

form .right-btn {
    text-align: right;
}

form button {
    margin-top: 10px;
    padding: 10px;
    width: 100px;
    border: 1px solid #484848;
    border-radius: 20px;
    background-color: #5419da;
    color: white;
}

form .form-error {
    color: red;
}
</style>
