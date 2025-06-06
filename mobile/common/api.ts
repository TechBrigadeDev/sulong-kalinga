import axios, { AxiosError } from "axios";
import { authStore } from "features/auth/auth.store";

console.log("API URL:", process.env.EXPO_PUBLIC_API_URL);

export const axiosClient = axios.create({
    baseURL: process.env.EXPO_PUBLIC_API_URL,
});

axiosClient.interceptors.response.use(
    (response) => {
        return response;
    },
    (error) => {
        if (error instanceof AxiosError) {
            if (error.status === 401) {
                const setToken = authStore.getState().setToken;
                const message = error.response?.data?.message;

                switch (message) {
                    case "Unauthenticated.":
                        setToken(null);
                        break;
                    default:
                        console.error("An error occurred:", message);
                        break;
                }
            }
        }
        // Handle errors
        return Promise.reject(error);
    },
);
