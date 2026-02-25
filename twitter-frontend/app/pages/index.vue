<template>
    <div class="flex-container">
        <SideNav :user="user" :refresh="refresh" />
        <div class="main-container">
            <div class="title">ホーム</div>
            <div class="tmp-message" v-if="pending">Loading ...</div>
            <div class="tmp-message" v-else-if="isEmpty">投稿はまだありません</div>
            <Message v-else v-for="post in postLists" :user="user" :post="post" :refresh="refresh" :isIndex="true"/>
        </div>
    </div>
</template>

<script setup lang="ts">
import auth from '../middleware/auth';

definePageMeta({
    middleware: auth,
});

const { user } = useFirebaseAuth();
const { fetchAPI } = useApi();

// 投稿情報の取得
const { data: fetchedData, refresh, pending } = await useAsyncData(
    'posts',
    async () => {
        const token = await user.value?.getIdToken();
        const res = await fetchAPI('/posts', {
            method: 'GET',
            headers: {
                Authorization: `Bearer ${token}`,
            },
        }) as { posts: any[]; message: string };
        console.log("[INFO] ", res.message);
        return res;
    },
    {
        watch: [user],  // userの状態が変わるたびに投稿情報を再取得
    }
)
const postLists = computed(() => fetchedData.value?.posts || []);
const isEmpty = computed(() => !pending.value && fetchedData.value && postLists.value.length === 0)
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

.main-container .title {
    padding: 10px;
    border-left: 1px solid white;
    border-bottom: 1px solid white;
    font-size: 20px;
}

.main-container .tmp-message {
    padding: 10px;
    border-left: 1px solid white;
    border-bottom: 1px solid white;
}
</style>
