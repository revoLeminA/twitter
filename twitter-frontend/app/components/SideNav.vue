<template>
    <div class="side-container">
        <img src="../assets/imgs/logo.png">
        <div class="nav-item">
            <a href="/">
                <img src="../assets/imgs/home.png" title="ホーム">
                <div class="nav-name">ホーム</div>
            </a>
        </div>
        <div class="nav-item">
            <a @click="handleLogout">
                <img src="../assets/imgs/logout.png" title="ログアウト">
                <div class="nav-name" v-bind:disabled="isLoadingLogout">
                    {{ isLoadingLogout ? 'ログアウト中...' : 'ログアウト' }}
                </div>
            </a>
        </div>
        <form @submit.prevent="handleCreatePost">
            <label>シェア</label>
            <textarea v-model="content"></textarea>
            <div class="form-error">{{ errors.content }}</div>
            <div class="right-btn">
                <button type="submit" v-bind:disabled="isLoadingCreatePost">
                    {{  isLoadingCreatePost ? 'シェア中...' : 'シェアする' }}
                </button>
            </div>
        </form>
    </div>
</template>

<script setup lang="ts">
import { useField, useForm } from 'vee-validate';
import * as yup from 'yup';

// バリデーションルール定義
const schema = yup.object({
    content: yup.string().required().max(120),
});
const { errors, resetForm } = useForm({
    validationSchema: schema
});
const { value: content } = useField<string>('content');

const { logout } = useFirebaseAuth();
const { fetchAPI } = useApi();
const props = defineProps<{
    user: any
    refresh: () => Promise<void>
}>();
const isLoadingLogout = ref(false);
const isLoadingCreatePost = ref(false);

// ログアウト処理
const handleLogout = async () => {
    isLoadingLogout.value = true;

    const { error } = await logout();

    isLoadingLogout.value = false;

    // ログアウト成功時はログイン画面へ遷移
    if (!error) {
        return navigateTo('/login');
    }
}

// 投稿登録処理
const handleCreatePost = async () => {
    isLoadingCreatePost.value = true;

    // Laravelのpostsテーブルに投稿を登録
    try {
        const token = await props.user?.getIdToken();
        const res = await fetchAPI('/posts', {
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
        props.refresh(); // 投稿成功時はホーム画面をリロードして新しい投稿を表示
    } catch (err) {
        console.error("[ERROR] Fail to store post:", err);
    }

    isLoadingCreatePost.value = false;
};
</script>

<style scoped>
.side-container {
    width: 20%;
    margin: 20px;
}

.side-container img {
    width: 100px;
    height: auto;
}

.side-container .nav-item {
    margin-top: 10px;
    margin-bottom: 10px;
}

.side-container .nav-item a {
    text-decoration: none;
    display: flex;
    align-items: center;
}

.side-container .nav-item a img {
    width: 20px;
    height: auto;
}

.side-container .nav-item a .nav-name {
    display: inline-block;
    margin-left: 15px;
    color: white;
}

.side-container form label {
    display: block;
    color: white;
}

.side-container form textarea {
    display: block;
    width: 90%;
    height: 100px;
    margin: 10px 0;
    padding: 10px;
    background-color: #15202b;
    border: 1px solid white;
    border-radius: 5px;
    color: white;
    resize: none;
}

.side-container form .right-btn {
    margin: 0 10px;
    text-align: right;
}

.side-container form button {
    margin: 10px auto;
    padding: 10px;
    width: 100px;
    border: 1px solid #484848;
    border-radius: 20px;
    background-color: #5419da;
    color: white;
}

.side-container form .form-error {
    color: red;
}
</style>
