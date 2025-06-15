import { useQuery } from "@tanstack/react-query";
import { QK } from "common/query";
import { authStore } from "features/auth/auth.store";

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
