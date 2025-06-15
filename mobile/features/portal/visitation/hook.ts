import { useQuery } from "@tanstack/react-query";
import { QK } from "common/query";
import { authStore } from "features/auth/auth.store";

import visitationController from "./api";

const api = visitationController;

export const useGetVisitations = () => {
    const { role, token } = authStore();
    return useQuery({
        queryKey: QK.visitations.getVisitations(),
        queryFn: async () => {
            const response = await api.getEvents(
                role!,
            );
            return response;
        },
        enabled: !!role && !!token,
    });
};
