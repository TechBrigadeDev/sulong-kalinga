import { QueryClient } from "@tanstack/react-query";

export const queryClient = new QueryClient();

export const QK = {
    auth: {
        login: "auth/login",
        logout: "auth/logout",
    },
    user: {
        management: {
            getBeneficiaries: "user/management/getBeneficiaries",
            getBeneficiary: (id: string) => ["user/management/getBeneficiary", id],
        }
    }
}