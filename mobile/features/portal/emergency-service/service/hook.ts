import {
    useMutation,
    useQuery,
} from "@tanstack/react-query";
import { invalidateQK, QK } from "common/query";
import { authStore } from "features/auth/auth.store";

import serviceController from "./api";
import { IServiceRequestForm } from "./form/schema";

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
            data: IServiceRequestForm,
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
