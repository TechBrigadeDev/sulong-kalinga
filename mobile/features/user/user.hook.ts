import {
    useMutation,
    useQuery,
} from "@tanstack/react-query";
import { authStore } from "features/auth/auth.store";
import { useMemo } from "react";

import { invalidateQK, QK } from "~/common/query";

import userController from "./user.api";
import {
    dtoEmailUpdate,
    dtoUpdatePassword,
    IStaffProfile,
} from "./user.interface";

export const useUserProfile = () => {
    const { token, role } = authStore();

    const query = useQuery({
        queryKey: QK.user.getUserProfile(
            token as string,
        ),
        queryFn: async () => {
            if (!token || !role) {
                throw new Error(
                    "Token is required",
                );
            }
            const response =
                await userController.getUserProfile(
                    role,
                );

            console.log(
                "User profile fetched:",
                response,
            );
            return response;
        },
        enabled: !!token,
    });

    const profileRole = query.data?.role;

    const isStaff =
        profileRole !== "beneficiary" &&
        profileRole !== "family_member";

    const staffData = useMemo<
        IStaffProfile | undefined
    >(() => {
        if (!isStaff) {
            return undefined;
        }
        return query.data as unknown as IStaffProfile;
    }, [isStaff, query.data]);

    return {
        ...query,
        isStaff,
        staffData,
    };
};

export const useUpdateEmail = (params?: {
    onSuccess: () => Promise<void>;
}) => {
    const { user, setUser } = authStore();
    const { role, token } = authStore();

    return useMutation({
        mutationFn: async (
            data: dtoEmailUpdate,
        ) => {
            if (!token || !role) {
                throw new Error(
                    "Token is required",
                );
            }

            const response =
                await userController.updateEmail(
                    data,
                    role!,
                );

            return response;
        },
        onSuccess: async (data) => {
            if (user) {
                if (user.role !== "beneficiary") {
                    setUser({
                        ...user,
                        email: data,
                    });
                }
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

export const useUpdatePassword = (params?: {
    onSuccess: () => Promise<void>;
}) => {
    const { token, role } = authStore();

    return useMutation({
        mutationFn: async (
            data: dtoUpdatePassword,
        ) => {
            if (!token || !role) {
                throw new Error(
                    "Token is required",
                );
            }

            const response =
                await userController.updatePassword(
                    data,
                    role!,
                );
            console.log(
                "Password updated successfully:",
                response,
            );
            return response;
        },
        onSuccess: async () => {
            if (params?.onSuccess) {
                await params.onSuccess();
            }
        },
        onError: (error) => {
            console.error(
                "Error updating password:",
                error,
            );
        },
    });
};
