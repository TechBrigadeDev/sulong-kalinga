import { useQuery } from "@tanstack/react-query";
import { QK } from "common/query";
import { authStore } from "features/auth/auth.store";

import carePlanController from "./api";

const api = carePlanController;

export const useCarePlans = () => {
    const { role, token } = authStore();

    return useQuery({
        queryKey: QK.carePlan.getCarePlans(),
        queryFn: async () => {
            const response =
                await api.getCarePlans(role!);

            return response;
        },
        enabled: !!role && !!token,
    });
};

export const useCarePlanById = (id: string) => {
    const { role, token } = authStore();

    return useQuery({
        queryKey: QK.carePlan.getCarePlanById(id),
        queryFn: async () => {
            const response =
                await api.getCarePlanById(
                    role!,
                    id,
                );

            return response;
        },
        enabled: !!role && !!token && !!id,
    });
};
