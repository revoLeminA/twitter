import { initializeApp, type FirebaseOptions } from 'firebase/app';
import { getAuth } from 'firebase/auth';

export default defineNuxtPlugin(() => {
    const config = useRuntimeConfig();

    const firebaseConfig: FirebaseOptions = {
        apiKey: config.public.firebaseApiKey as string,
        authDomain: config.public.firebaseAuthDomain as string,
        projectId: config.public.firebaseProjectId as string,
        storageBucket: config.public.firebaseStorageBucket as string,
        messagingSenderId: config.public.firebaseMessagingSenderId as string,
        appId: config.public.firebaseAppId as string,
    };

    // Firebaseアプリの初期化
    const app = initializeApp(firebaseConfig);

    // Firebase Authenticationの初期化
    const auth = getAuth(app);

    return {
        provide: {
            auth,
        },
    };
});
