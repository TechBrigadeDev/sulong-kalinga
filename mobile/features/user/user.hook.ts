import {
    useMutation,
    useQuery,
} from "@tanstack/react-query";
import { authStore } from "features/auth/auth.store";

import { invalidateQK, QK } from "~/common/query";

import userController from "./user.api";
import { IEmailUpdate } from "./user.interface";
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
};

export const useUpdateEmail = (params?: {
    onSuccess: () => Promise<void>;
}) => {
    const { user, setUser } = userStore();
    const { token } = authStore();

    return useMutation({
        mutationFn: async (
            data: IEmailUpdate,
        ) => {
            if (!token) {
                throw new Error(
                    "Token is required",
                );
            }

            const response =
                await userController.updateEmail(
                    data,
                    token,
                );
            console.log(
                "Email updated successfully:",
                response,
            );
            return response;
        },
        onSuccess: async (data) => {
            if (user) {
                setUser({
                    ...user,
                    email: data,
                });
            }

            if (params?.onSuccess) {
                await params.onSuccess();
            }
        },
        onSettled: async () => {
            await Promise.all([
                invalidateQK(
                    QK.user.getUser(
                        token as string,
                    ),
                ),
                invalidateQK(
                    QK.user.getUserProfile(
                        token as string,
                    ),
                ),
            ]);
        },
    });
};
