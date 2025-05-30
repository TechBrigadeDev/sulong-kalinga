import { useRouter } from "expo-router";
import { authStore } from "./auth.store"
import { useMutation } from "@tanstack/react-query";
import { QK } from "~/common/query";
import authController from "./auth.api";

export const useLogin = () => {
    const { 
        setToken
    } = authStore();

    const {
        mutateAsync: login,
        isPending
    } = useMutation({
        mutationKey: [QK.auth.login],
        mutationFn: async (data: { email: string; password: string }) => {
            const response = await authController.login(data.email, data.password);
            return response;
        },
        onSuccess: (data) => {
            setToken(data.token);
        },
        onError: (error) => {
            console.error("Login failed", error);
        }
    })

    return {
        login,
        isPending
    }
}


export const useLogout = () => {
    const { 
        token,
        setToken
    } = authStore();
    const router = useRouter();

    const {
        mutateAsync: logout,
        isPending
    } = useMutation({
        mutationKey: [QK.auth.logout, token],
        mutationFn: async () => {
            if (!token) {
                throw new Error("No token found");
            }
            await authController.logout(token);
        },
        onSettled: () => {
            setToken(null);
            router.replace("/login");
        },
        onError: (error) => {
            console.error("Logout failed", error);
        }
    })

    return {
        logout,
        isPending
    }
}