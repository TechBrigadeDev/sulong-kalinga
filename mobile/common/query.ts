import { QueryClient } from "@tanstack/react-query";

export const queryClient = new QueryClient();

export const QK = {
    auth: {
        login: "auth/login",
        logout: "auth/logout",
    },
    user: {
        getUser: (token: string) => [
            "user/getUser",
            token,
        ],
        management: {
            getBeneficiaries:
                "user/management/getBeneficiaries",
            getBeneficiary: (id: string) => [
                "user/management/getBeneficiary",
                id,
            ],
            getFamilyMembers: (params: {
                search: string;
            }) => [
                "user/management/getFamilyMembers",
                params.search,
            ],
            getFamilyMember: (id: string) => [
                "user/management/getFamilyMember",
                id,
            ],
            getCareWorkers:
                "user/management/getCareWorkers",
            getCareWorker: (id: string) => [
                "user/management/getCareWorker",
                id,
            ],
            getCareManagers:
                "user/management/getCareManagers",
            getCareManager: (id: string) => [
                "user/management/getCareManager",
                id,
            ],
            getAdministrators:
                "user/management/getAdministrators",
            getAdmin: (id: string) => [
                "user/management/getAdmin",
                id,
            ],
        },
    },
    scheduling: {
        medication: {
            getSchedules:
                "user/scheduling/medication/getSchedules",
            getSchedule: (id: string) => [
                "user/scheduling/medication/getSchedule",
                id,
            ],
        },
        visitation: {
            getVisitations:
                "user/scheduling/visitation/getVisitations",
            getVisitation: (id: string) => [
                "user/scheduling/visitation/getVisitation",
                id,
            ],
        },
    },
};

export const setDataQK = async (
    key: string[],
    data: any,
) => {
    queryClient.setQueryData(key, data);
};
