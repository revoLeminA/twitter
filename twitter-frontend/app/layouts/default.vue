<template>
    <div>
        <slot></slot>
    </div>
</template>

<script setup lang="ts">
const { initAuth } = useFirebaseAuth();

// アプリ起動時に認証状態を監視
onMounted(() => {
    initAuth();
});
</script>
