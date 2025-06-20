import {
    useMutation,
    useQuery,
} from "@tanstack/react-query";
import { invalidateQK, QK } from "common/query";
import { authStore } from "features/auth/auth.store";

import { IServiceForm } from "./_components/form/interface";
import serviceController from "./api";

const api = serviceController;

export const useServiceTypes = () => {
    const { role, token } = authStore();
    return useQuery({
        queryKey: [
            QK.emergencyService.service.getTypes,
        ],
        queryFn: async () => {
            const response =
                await api.getServiceTypes(role!);
            return response;
        },
        enabled: !!role && !!token,
    });
};

export const useServiceRequest = () => {
    const { role, token } = authStore();
    return useMutation({
        mutationFn: async (
            data: IServiceForm,
        ) => {
            if (!role || !token) {
                throw new Error(
                    "User role or token is not defined",
                );
            }

            const response =
                await api.postServiceRequest(
                    role,
                    data,
                );
            return response;
        },
        onSuccess: async (data) => {
            console.log(
                "Service request submitted successfully:",
                data,
            );

            await invalidateQK(
                QK.emergencyService.getActiveRequests(),
            );
            await invalidateQK(
                QK.emergencyService.getRequestsHistory(),
            );
        },
    });
};

export const useEditnServiceRequest = () => {
    const { role, token } = authStore();
    return useMutation({
        mutationFn: async ({
            id,
            data,
        }: {
            data: IServiceForm;
            id: string;
        }) => {
            if (!role || !token) {
                throw new Error(
                    "User role or token is not defined",
                );
            }

            const response =
                await api.putServiceRequest(
                    role,
                    id,
                    data,
                );
            return response;
        },
        onSuccess: async (data) => {
            console.log(
                "Service request edited successfully:",
                data,
            );

            await invalidateQK(
                QK.emergencyService.getActiveRequests(),
            );
            await invalidateQK(
                QK.emergencyService.getRequestsHistory(),
            );
        },
    });
};
