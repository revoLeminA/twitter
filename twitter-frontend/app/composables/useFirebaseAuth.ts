import {
	signInWithEmailAndPassword,
	createUserWithEmailAndPassword,
	signOut,
	onAuthStateChanged,
	type User,
    type Auth,
} from "firebase/auth";

export const useFirebaseAuth = () => {
    const nuxtApp = useNuxtApp();
	const auth = nuxtApp.$auth;
	const user = useState<User | null>("firebase-user", () => null);
    const isLoading = useState("auth-loading", () => true);

    // 認証状態の監視
    const initAuth = () => {
        if (!auth) {
			return;
		}
		onAuthStateChanged(auth, (firebaseUser) => {
			user.value = firebaseUser;
            isLoading.value = false;
        });
	};

	// メールアドレスとパスワードでサインアップ
	const signUp = async (email: string, password: string) => {
		try {
			const userCredential = await createUserWithEmailAndPassword(
				auth,
				email,
				password,
			);
			return { user: userCredential.user, error: null };
		} catch (error: any) {
			return { user: null, error: error.message };
		}
	};

	// メールアドレスとパスワードでサインイン
	const signIn = async (email: string, password: string) => {
		try {
			const userCredential = await signInWithEmailAndPassword(
				auth,
				email,
				password,
			);
			return { user: userCredential.user, error: null };
		} catch (error: any) {
			return { user: null, error: error.message };
		}
	};

	// サインアウト
	const logout = async () => {
        try {
			await signOut(auth);
			user.value = null;
			return { error: null };
		} catch (error: any) {
			return { error: error.message };
		}
	};

	return {
		user,
		isLoading,
		initAuth,
		signUp,
		signIn,
		logout,
	};
};
