import {
    useMutation,
    useQuery,
} from "@tanstack/react-query";
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

export const useAcknowledgeCarePlan = (
    id: string,
) => {
    const { role, token } = authStore();
    const { refetch } = useCarePlans();

    return useMutation({
        mutationKey:
            QK.carePlan.acknowledgeCarePlan(id),
        mutationFn: async () => {
            if (!role || !token) {
                throw new Error(
                    "User role or token is not available",
                );
            }

            const response =
                await api.acknowledgeCarePlan(
                    role!,
                    id,
                );

            return response;
        },
        onSuccess: async () => {
            await refetch();
        },
    });
};
