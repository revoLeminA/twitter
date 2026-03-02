export const useApi = () => {
    const config = useRuntimeConfig();

    // Laravel APIと通信するための共通関数
    const fetchAPI = async<T>(
        endPoint: string,
        options: any = {}
    ): Promise<T> => {
        return await $fetch<T>(`${config.public.apiBase}${endPoint}`, {
            ...options,
        });
    };

    return {
        fetchAPI,
    };
}
