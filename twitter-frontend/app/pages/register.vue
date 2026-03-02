<template>
    <AuthHeader />
    <div class="auth-container">
        <label>新規登録</label>
        <form @submit.prevent="handleRegister">
            <div class="form-group">
                <input type="text" v-model="name" placeholder="ユーザネーム">
                <div class="form-error">{{ errors.name }}</div>
            </div>
            <div class="form-group">
                <input type="email" v-model="email" placeholder="メールアドレス">
                <div class="form-error">{{ errors.email }}</div>
            </div>
            <div class="form-group">
                <input type="password" v-model="password" placeholder="パスワード">
                <div class="form-error">{{ errors.password }}</div>
            </div>
            <button class="form-btn" type="submit" v-bind:disabled="isLoadingRegister">
                {{ isLoadingRegister ? '新規登録中...' : '新規登録' }}
            </button>
        </form>
    </div>
</template>

<script setup lang="ts">
import { useField, useForm } from 'vee-validate';
import * as yup from 'yup';

// バリデーションルール定義
const schema = yup.object({
    name: yup.string().required().max(20),
    email: yup.string().required().email(),
    password: yup.string().required().min(6)
});
const { errors } = useForm({
    validationSchema: schema
});
const { value: name } = useField<string>('name');
const { value: email } = useField<string>('email');
const { value: password } = useField<string>('password');

const { signUp } = useFirebaseAuth();
const { fetchAPI } = useApi();
const router = useRouter();
const isLoadingRegister = ref(false);

// 新規登録処理
const handleRegister = async () => {
    isLoadingRegister.value = true;

    // Firebase Authenticationでユーザ登録
    const { user, error: signUpError } = await signUp(
        email.value,
        password.value,
    )

    let isSuccess = false;

    // Laravelのusersテーブルにユーザ情報を登録
    try {
        const token = await user?.getIdToken();
        const res = await fetchAPI('/auth', {
            method: "POST",
            headers: {
                Authorization: `Bearer ${token}`,
            },
            body: {
                name: name.value,
            },
        }) as { message: string };
        console.log("[INFO] ", res.message);
        isSuccess = true;
    } catch (err) {
        console.error("[ERROR] Fail to store user:", err);
        isSuccess = false;
    }

    isLoadingRegister.value = false;

    // 登録成功時はホーム画面へ遷移
    if (!signUpError && user && isSuccess) {
        await router.push('/');
    }
};
</script>

<style scoped>
.auth-container {
    background-color: white;
    margin: 50px auto;
    padding: 10px;
    max-width: 400px;
    border-radius: 5px;
    text-align: center;
}

.auth-container label {
    font-weight: bold;
}

.auth-container form {
    margin: 10px 0;
}

.auth-container form .form-group {
    margin: 0px auto;
    margin-left: 30px;
    margin-bottom: 10px;
    text-align: left;
}

.auth-container form .form-group input {
    padding: 10px;
    width: 80%;
    border: 1px solid #484848;
    border-radius: 5px;
}

.auth-container form .form-group .form-error {
    color: red;
}

.auth-container form .form-btn {
    display: block;
    margin: 10px auto;
    padding: 10px;
    width: 100px;
    border: 1px solid #484848;
    border-radius: 20px;
    background-color: #5419da;
    color: white;
}
</style>
