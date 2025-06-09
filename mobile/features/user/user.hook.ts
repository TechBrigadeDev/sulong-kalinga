import { useQuery } from "@tanstack/react-query";
import { authStore } from "features/auth/auth.store";

import { QK } from "~/common/query";

import userController from "./user.api";
import { userStore } from "./user.store";

export const useUser = () => {
    const { token } = authStore();
    const { setUser } = userStore();

    return useQuery({
        queryKey: QK.user.getUser(
            token as string,
        ),
        queryFn: async () => {
            if (!token) {
                throw new Error(
                    "Token is required",
                );
            }
            const response =
                await userController.getUser(
                    token,
                );

            setUser(response);
            console.log(
                "User data fetched:",
                response,
            );
            return response;
        },
        enabled: !!token,
    });
};


export const useUserProfile = () => {
    const { token } = authStore();

    return useQuery({
        queryKey: QK.user.getUserProfile(
            token as string,
        ),
        queryFn: async () => {
            if (!token) {
                throw new Error(
                    "Token is required",
                );
            }
            const response =
                await userController.getUserProfile(
                    token,
                );

            console.log(
                "User profile fetched:",
                response,
            );
            return response;
        },
        enabled: !!token,
    });
}