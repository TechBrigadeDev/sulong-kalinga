import { QueryClient } from "@tanstack/react-query";

export const queryClient = new QueryClient();

export const QK = {
    auth: {
        login: "auth/login",
        logout: "auth/logout",
    },
    user: {
        getUser: (token: string) => ["user/getUser", token],
        management: {
            getBeneficiaries: "user/management/getBeneficiaries",
            getBeneficiary: (id: string) => ["user/management/getBeneficiary", id],
            getFamilyMembers: (
                params: {
                    search: string;
                }
            ) => [
                "user/management/getFamilyMembers",
                params.search,
            ],
            getFamilyMember: (id: string) => [
                "user/management/getFamilyMember",
                id,
            ],
            getCareWorkers: "user/management/getCareWorkers",
            getCareWorker: (id: string) => ["user/management/getCareWorker", id],
        }
    }
}