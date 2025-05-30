import { useQuery } from "@tanstack/react-query";
import { QK } from "~/common/query"
import { authStore } from "~/features/auth/auth.store"
import UserManagementController from "./management.api";


export const useGetBeneficiaries = (props?: {
    search?: string;
}) => {   
    const { token } = authStore();
    if (!token) {
        throw new Error("No token found");
    }

    const api = new UserManagementController(token);
    return useQuery({
        queryKey: [QK.user.management.getBeneficiaries, token, props?.search],
        queryFn: async () => {
            const response = await api.getBeneficiaries({
                search: props?.search,
            });
            return response;
        },
        enabled: !!token,
    })
}

export const useGetBeneficiary = (id?: string) => {
    const { token } = authStore();
    if (!token || !id) {
        throw new Error("No token found");
    }

    const api = new UserManagementController(token);

    return useQuery({
        queryKey: QK.user.management.getBeneficiary(id),
        queryFn: async () => {
            const response = await api.getBeneficiary(id);
            return response;
        },
        enabled: !!token,
    })
}

export const useGetFamilyMembers = (props?: {
    search?: string;
}) => {
    const { token } = authStore();
    if (!token) {
        throw new Error("No token found");
    }

    const api = new UserManagementController(token);
    return useQuery({
        queryKey: QK.user.management.getFamilyMembers({
            search: props?.search || "",
        }),
        queryFn: async () => {
            const response = await api.getFamilyMembers();
            return response;
        },
        enabled: !!token,
    })
}

export const useGetFamilyMember = (id?: string) => {
    const { token } = authStore();
    if (!token || !id) {
        throw new Error("No token found");
    }

    const api = new UserManagementController(token);
    console.log(token, id);

    return useQuery({
        queryKey: QK.user.management.getFamilyMember(id),
        queryFn: async () => {
            const response = await api.getFamilyMember(id);
            return response;
        },
        enabled: !!token,
    })
}

export const useGetCareWorkers = (props?: {
    search?: string;
}) => {
    const { token } = authStore();
    if (!token) {
        throw new Error("No token found");
    }

    const api = new UserManagementController(token);
    return useQuery({
        queryKey: [QK.user.management.getCareWorkers, token, props?.search],
        queryFn: async () => {
            const response = await api.getCareWorkers({
                search: props?.search,
            });
            return response;
        },
        enabled: !!token,
    })
}