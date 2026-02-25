import { setLocale } from 'yup';

export default defineNuxtPlugin(() => {
    setLocale({
        mixed: {
            required: "この項目は必須です",
        },
        string: {
            email: "正しい形式のメールアドレスを入力してください",
            min: ({ min }) => `${min}文字以上で入力してください`,
            max: ({max}) => `${max}文字以下で入力してください`,
        }
    });
});
