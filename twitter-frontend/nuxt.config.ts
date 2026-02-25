// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
	compatibilityDate: "2025-07-15",
    devtools: { enabled: true },
    css: [
        './app/assets/css/global.css'
    ],
    runtimeConfig: {
        public: {
            firebaseApiKey: process.env.NUXT_PUBLIC_FIREBASE_API_KEY,
            firebaseAuthDomain: process.env.NUXT_PUBLIC_FIREBASE_AUTH_DOMAIN,
            firebaseProjectId: process.env.NUXT_PUBLIC_FIREBASE_PROJECT_ID,
            firebaseStorageBucket: process.env.NUXT_PUBLIC_FIREBASE_STORAGE_BUCKET,
            firebaseMessagingSenderId: process.env.NUXT_PUBLIC_FIREBASE_MESSAGING_SENDER_ID,
            firebaseAppId: process.env.NUXT_PUBLIC_FIREBASE_APP_ID,
            apiBase: process.env.NUXT_PUBLIC_API_BASE || 'http://localhost/api',
        },
    },
});
