import {
    useInfiniteQuery,
    useMutation,
    useQuery,
} from "@tanstack/react-query";
import { isDev } from "common/env";
import { invalidateQK, QK } from "common/query";
import { authStore } from "features/auth/auth.store";

import notificationController from "./api";
import { registerForPushNotification } from "./service";

const api = notificationController;

export const useNotifications = (props?: {
    search?: string;
    limit?: number;
}) => {
    const { role, token } = authStore();

    return useInfiniteQuery({
        queryKey: [
            QK.notification.getNotifications,
            token,
        ],
        queryFn: async ({ pageParam = 1 }) => {
            const response =
                await api.getNotifications({
                    role: role!,
                    limit: props?.limit || 10,
                    search: props?.search || "",
                    page: pageParam,
                });
            const data = response.data;

            return {
                data: data.data,
                meta: {
                    current_page:
                        data.current_page,
                    last_page: data.last_page,
                    total: data.total,
                    per_page: data.per_page,
                },
            };
        },
        getNextPageParam: (lastPage) =>
            lastPage.meta.current_page <
            lastPage.meta.last_page
                ? lastPage.meta.current_page + 1
                : undefined,
        initialPageParam: 1,
        enabled: !!role && !!token,
        refetchOnMount: true,
        refetchOnWindowFocus: true,
        staleTime: 1000 * 60 * 5,
        refetchInterval: 1000 * 60,
    });
};

export const useReadNotification = () => {
    const { role, token } = authStore();

    return useMutation({
        mutationFn: async (id: string) => {
            if (!role || !token) {
                throw new Error(
                    "Role or token is not defined",
                );
            }

            return await api.readNotification(
                role,
                id,
            );
        },
        onSuccess: async () => {
            await invalidateQK([
                QK.notification.getNotifications,
                token!,
            ]);
        },
    });
};

export const useReadAllNotifications = () => {
    const { role, token } = authStore();

    return useMutation({
        mutationFn: async () => {
            if (!role || !token) {
                throw new Error(
                    "Role or token is not defined",
                );
            }

            return await api.readAllNotifications(
                role,
            );
        },
        onSuccess: async () => {
            await invalidateQK([
                QK.notification.getNotifications,
                token!,
            ]);
        },
    });
};

export const useGetNotificationToken = () => {
    const { role } = authStore();

    return useQuery({
        queryKey: QK.notification.getToken(role!),
        queryFn: async () => {
            if (!role) {
                throw new Error(
                    "Role is not defined",
                );
            }

            return await api.getNotificationToken(
                role,
            );
        },
        enabled: !!role,
        staleTime: 1000 * 60 * 60 * 24,
    });
};

export const useRegisterNotification = () => {
    const { data: notificationToken } =
        useGetNotificationToken();

    const { role } = authStore();

    return useQuery({
        queryKey: QK.notification.registerToken(
            role!,
            notificationToken,
        ),
        queryFn: async () => {
            if (!role) {
                throw new Error(
                    "Token is not defined",
                );
            }

            if (!notificationToken) {
                const token =
                    await registerForPushNotification();

                if (!token) {
                    throw new Error(
                        "Failed to get notification token",
                    );
                }

                console.log(
                    "Register token:",
                    token,
                );

                await api.registerNotification({
                    role,
                    token,
                });
            }

            return "yellow";
        },
        enabled: !!role,
        staleTime: isDev
            ? Infinity
            : 1000 * 60 * 60 * 24,
        // ...(isDev
        //     ? {
        //           refetchInterval: 5000,
        //       }
        //     : {}),
    });
};
