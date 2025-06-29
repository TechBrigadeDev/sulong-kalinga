import axios, {
    AxiosError,
    AxiosInstance,
} from "axios";
import { IRole } from "features/auth/auth.interface";
import { authStore } from "features/auth/auth.store";
import { showToastable } from "react-native-toastable";

console.log(
    "API URL:",
    process.env.EXPO_PUBLIC_API_URL,
);

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
                const setToken =
                    authStore.getState().setToken;
                const message =
                    error.response?.data?.message;

                switch (message) {
                    case "Unauthenticated.":
                        console.warn(
                            "Session expired, redirecting to login.",
                            //path
                            error.response
                                ?.headers,
                            error.request
                                .responseURL,
                        );
                        // Clear the token and redirect to login
                        setToken(null);
                        showToastable({
                            title: "Session expired",
                            status: "danger",
                            message:
                                "Please log in again.",
                        });
                        break;
                    default:
                        console.error(
                            "An error occurred:",
                            message,
                        );
                        break;
                }
            }
        }
        // Handle errors
        return Promise.reject(error);
    },
);

export class Controller {
    public role: IRole | null = null;

    constructor(
        public api: AxiosInstance = axiosClient,
    ) {
        this.api = api;
        this.api.defaults.headers.common[
            "Accept"
        ] = "application/json";
        this.api.defaults.headers.common[
            "Content-Type"
        ] = "application/json";
        authStore.subscribe((state) => {
            if (state.token) {
                this.api.defaults.headers.common[
                    "Authorization"
                ] = `Bearer ${state.token}`;
            } else {
                delete this.api.defaults.headers
                    .common["Authorization"];
            }
        });

        authStore.subscribe((state) => {
            if (state.role) {
                this.role = state.role;
            }
        });
    }
}
