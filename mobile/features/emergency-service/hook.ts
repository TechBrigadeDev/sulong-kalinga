import { useQuery } from "@tanstack/react-query";
import { QK } from "common/query";
import { authStore } from "features/auth/auth.store";

import emergencyServiceController from "./api";

const api = emergencyServiceController;

export const useEmergencyServiceRequests = () => {
    const { token, role } = authStore();
    return useQuery({
        queryKey:
            QK.emergencyService.getActiveRequests(),
        queryFn: async () => {
            const response =
                await api.getActiveRequests(
                    role!,
                );
            return response.data;
        },
        enabled: !!role && !!token,
    });
};

export const useEmergencyServiceRequestsHistory =
    () => {
        const { token, role } = authStore();
        return useQuery({
            queryKey:
                QK.emergencyService.getRequestsHistory(),
            queryFn: async () => {
                const response =
                    await api.getRequestsHistory(
                        role!,
                    );
                return response.data;
            },
            enabled: !!role && !!token,
        });
    };
