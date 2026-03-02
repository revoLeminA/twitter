<template>
    <div class="message-container">
        <div class="flex-items">
            <div class="flex-item">{{ props.post.user.name }}</div>
            <div class="flex-item">
                <button @click="handleToggleLike(props.post.id)">
                    <img src="../assets/imgs/heart.png" title="いいね">
                </button>
            </div>
            <div class="flex-item" :class="{ liked: props.post.is_liked }">{{ props.post.likes_count }}</div>
            <div class="flex-item">
                <button @click="handleDeletePost(props.post.id)">
                    <img src="../assets/imgs/cross.png" title="削除">
                </button>
            </div>
            <div class="flex-item" v-if="props.isIndex">
                <a :href="`/${props.post.id}`">
                    <img src="../assets/imgs/detail.png" title="詳細">
                </a>
            </div>
        </div>
        {{ props.post.content }}
    </div>
</template>

<script setup lang="ts">
const { fetchAPI } = useApi();
const props = defineProps<{
    user: any
    post: {
        id: number
        content: string
        user: { name: string }
        likes_count: number
        is_liked: boolean
    }
    refresh: () => Promise<void>
    isIndex: boolean
}>();

// 投稿削除処理
const handleDeletePost = async (postId: number) => {
    if (!confirm("この投稿を本当に削除しますか？")) {
        return;
    }

    try {
        const token = await props.user?.getIdToken();
        const res = await fetchAPI(`/posts/${postId}`, {
            method: 'DELETE',
            headers: {
                Authorization: `Bearer ${token}`,
            },
        }) as { message: string };
        console.log("[INFO] ", res.message);
        props.refresh();  // 投稿削除後に投稿情報を再取得して反映
    } catch (err) {
        console.error("[ERROR] Fail to delete post:", err);
    }
}

const handleToggleLike = async (postId: number) => {
    try {
        const token = await props.user?.getIdToken();
        const res = await fetchAPI(`/posts/${postId}/like`, {
            method: 'PATCH',
            headers: {
                Authorization: `Bearer ${token}`,
            },
        }) as { message: string };
        console.log("[INFO] ", res.message);
        props.refresh();  // いいねのトグル後に投稿情報を再取得して反映
    } catch (err) {
        console.error("[ERROR] Fail to toggle like:", err);
    }
}
</script>

<style scoped>
.message-container {
    padding: 10px;
    border-left: 1px solid white;
    border-bottom: 1px solid white;
}

.flex-items {
    display: flex;
    align-items: center;
}

.flex-item {
    margin-right: 10px;
}

.flex-item:first-of-type {
    font-size: 20px;
    font-weight: bold;
    vertical-align: middle;
}

.flex-item img {
    width: 20px;
    height: auto;
    vertical-align: middle;
}

.flex-item:last-child {
    margin-left: 20px;
}

.flex-item button {
    background-color: transparent;
    border: none;
    cursor: pointer;
    appearance: none;
    vertical-align: middle;
}

.liked {
    color: red;
}
</style>
