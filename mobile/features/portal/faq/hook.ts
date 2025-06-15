import { useQuery } from "@tanstack/react-query";
import { QK } from "common/query";
import { authStore } from "features/auth/auth.store";

import faqController from "./api";

const api = faqController;

export const useGetFAQ = () => {
    const { role, token } = authStore();

    return useQuery({
        queryKey: QK.getFAQ(),
        queryFn: async () => {
            const response = await api.getFAQs(
                role!,
            );
            return response;
        },
        enabled: !!role && !!token,
    });
};
