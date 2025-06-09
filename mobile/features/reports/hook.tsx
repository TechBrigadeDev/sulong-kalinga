import { useQuery } from "@tanstack/react-query";
import { QK } from "common/query";
import { authStore } from "features/auth/auth.store";

import { reportsController } from "./api";

const api = reportsController;

export const useCarePlans = () => {
    const { token } = authStore((state) => state);

    return useQuery({
        queryKey: [QK.report.getReports],
        queryFn: async () => {
            const response =
                await api.getReports();
            return response;
        },
        enabled: !!token,
    });
};
