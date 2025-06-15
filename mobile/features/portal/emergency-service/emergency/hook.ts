import { useQuery } from "@tanstack/react-query";
import { QK } from "common/query";
import { authStore } from "features/auth/auth.store";

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
