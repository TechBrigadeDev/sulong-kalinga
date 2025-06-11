import { useToastController } from "@tamagui/toast";
import { useMutation } from "@tanstack/react-query";
import { AxiosError } from "axios";
import { useRouter } from "expo-router";

import { QK } from "~/common/query";

import authController from "./auth.api";
import { authStore } from "./auth.store";

export const useLogin = (params?: {
    onSuccess?: () => void;
}) => {
    const router = useRouter();
    const { setToken } = authStore();
    const toast = useToastController();

    const { mutateAsync: login, isPending } =
        useMutation({
            mutationKey: [QK.auth.login],
            mutationFn: async (data: {
                login: string;
                password: string;
            }) => {
                const response =
                    await authController.login(
                        data.login,
                        data.password,
                    );

                if (!response.success) {
                    throw new Error(
                        "Login failed",
                    );
                }

                return response;
            },
            onSuccess: (data) => {
                setToken(data.token);
                if (params?.onSuccess) {
                    params.onSuccess();
                }
            },
            onError: (error) => {
                if (error instanceof AxiosError) {
                    switch (
                        error.response?.status
                    ) {
                        case 401:
                            toast.show(
                                error.response
                                    ?.data
                                    ?.message ||
                                    "Invalid credentials",
                                {
                                    burntOptions:
                                        {
                                            preset: "error",
                                        },
                                },
                            );
                            break;
                        default:
                            break;
                    }
                }
                setToken(null);
                return;
            },
        });

    return {
        login,
        isPending,
    };
};

export const useLogout = () => {
    const { token, setToken } = authStore();
    const router = useRouter();

    const { mutateAsync: logout, isPending } =
        useMutation({
            mutationKey: [QK.auth.logout, token],
            mutationFn: async () => {
                if (!token) {
                    throw new Error(
                        "No token found",
                    );
                }
                await authController.logout(
                    token,
                );
            },
            onSettled: () => {
                setToken(null);
                router.replace("/login");
            },
            onError: (error) => {
                console.error(
                    "Logout failed",
                    error,
                );
            },
        });

    return {
        logout,
        isPending,
    };
};
