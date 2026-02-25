<template>
    <AuthHeader />
    <div class="auth-container">
        <label>ログイン</label>
        <form @submit.prevent="handleLogin">
            <div class="form-group">
                <input type="email" v-model="email" placeholder="メールアドレス">
                <div class="form-error">{{ errors.email }}</div>
            </div>
            <div class="form-group">
                <input type="password" v-model="password" placeholder="パスワード">
                <div class="form-error">{{ errors.password }}</div>
            </div>
            <button class="form-btn" type="submit" v-bind:disabled="isLoadingLogin">
                {{ isLoadingLogin ? 'ログイン中...' : 'ログイン'}}
            </button>
        </form>
    </div>
</template>

<script setup lang="ts">
import { useField, useForm } from 'vee-validate';
import * as yup from 'yup';

// バリデーションルール定義
const schema = yup.object({
    email: yup.string().required().email(),
    password: yup.string().required().min(6)
});
const { errors } = useForm({
    validationSchema: schema
});
const { value: email} = useField<string>('email');
const { value: password } = useField<string>('password');

const { signIn } = useFirebaseAuth();
const router = useRouter();
const isLoadingLogin = ref(false);

// ログイン処理
const handleLogin = async () => {
    isLoadingLogin.value = true;

    // Firebase Authenticationでログイン
    const { user, error: signInError } = await signIn(
        email.value,
        password.value,
    );

    isLoadingLogin.value = false;

    // ログイン成功時はホーム画面へ遷移
    if (!signInError && user) {
        await router.push('/');
    }
}
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
