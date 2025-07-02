import {
    useMutation,
    useQuery,
} from "@tanstack/react-query";
import { invalidateQK, QK } from "common/query";
import { authStore } from "features/auth/auth.store";

import { IEmergencyForm } from "./_components/form/interface";
import emergencyController from "./api";

const api = emergencyController;

export const useEmergencyTypes = () => {
    const { role, token } = authStore();
    return useQuery({
        queryKey: [
            QK.emergencyService.emergency
                .getTypes,
        ],
        queryFn: async () => {
            const response =
                await api.getEmergencyTypes(
                    "beneficiary",
                );

            return response;
        },
        enabled: !!role && !!token,
    });
};

export const useEmergencyRequest = () => {
    const { role, token } = authStore();
    return useMutation({
        mutationFn: async (
            data: IEmergencyForm,
        ) => {
            if (!role || !token) {
                throw new Error(
                    "User role or token is not available",
                );
            }
            const response =
                await api.postEmergencyRequest(
                    data,
                    role,
                );
            return response;
        },
        onSuccess: async () => {
            await invalidateQK(
                QK.emergencyService.getActiveRequests(),
            );
            await invalidateQK(
                QK.emergencyService.getRequestsHistory(),
            );
        },
    });
};

export const useEmergencyDeleteRequest = () => {
    const { role, token } = authStore();
    return useMutation({
        mutationFn: async (id: string) => {
            if (!role || !token) {
                throw new Error(
                    "User role or token is not available",
                );
            }
            const response =
                await api.deleteEmergencyRequest(
                    id,
                    role,
                );
            return response;
        },
        onSuccess: async () => {
            await invalidateQK(
                QK.emergencyService.getActiveRequests(),
            );
            await invalidateQK(
                QK.emergencyService.getRequestsHistory(),
            );
        },
    });
};

export const useEmergencyCancelRequest = () => {
    const { role, token } = authStore();
    return useMutation({
        mutationFn: async (id: string) => {
            if (!role || !token) {
                throw new Error(
                    "User role or token is not available",
                );
            }
            const response =
                await api.cancelEmergencyRequest(
                    id,
                    role,
                );
            return response;
        },
        onSuccess: async () => {
            await invalidateQK(
                QK.emergencyService.getActiveRequests(),
            );
            await invalidateQK(
                QK.emergencyService.getRequestsHistory(),
            );
        },
    });
};
