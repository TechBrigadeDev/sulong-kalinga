import { useQuery } from "@tanstack/react-query";
import { QK } from "common/query";
import { authStore } from "features/auth/auth.store";

import familyPortalController from "./api";

const api = familyPortalController;

export const useGetFamilyMembers = () => {
    const { token, role } = authStore();

    return useQuery({
        queryKey: QK.family.getFamilyMembers(),
        queryFn: async () => {
            const response =
                await api.getFamilyMembers(role!);
            return response;
        },
        enabled: !!role && !!token,
    });
};
