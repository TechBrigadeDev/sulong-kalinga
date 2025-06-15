import { useQuery } from "@tanstack/react-query";
import { QK } from "common/query";
import { authStore } from "features/auth/auth.store";

import medicationController from "./api";

const api = medicationController;

export const useGetMedications = () => {
    const { token, role } = authStore();

    return useQuery({
        queryKey: QK.medication.getMedications(),
        queryFn: async () => {
            const response =
                await api.getMedications(role!);
            return response;
        },
        enabled: !!token && !!role,
    });
};
