export default defineNuxtRouteMiddleware(async (to, from) => {
    if (import.meta.server) return;

    const { user, isLoading } = useFirebaseAuth();

	// ローディング中は待機
    if (isLoading.value) {
        return;
    }

	// 未認証の場合はログインページへリダイレクト
	if (!user.value) {
		return navigateTo("/login");
	}
});
