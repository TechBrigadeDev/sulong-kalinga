import {
    useInfiniteQuery,
    useQuery,
} from "@tanstack/react-query";

import { QK } from "~/common/query";
import { authStore } from "~/features/auth/auth.store";

import UserManagementController from "./management.api";

export const useGetBeneficiaries = (props?: {
    search?: string;
    limit?: number;
}) => {
    const { token } = authStore();
    if (!token) {
        throw new Error("No token found");
    }

    const api = new UserManagementController();

    return useInfiniteQuery({
        queryKey: [
            QK.user.management.getBeneficiaries,
            token,
            props?.search,
            props?.limit,
        ],
        queryFn: async ({ pageParam = 1 }) => {
            const response =
                await api.getBeneficiaries({
                    search: props?.search,
                    page: pageParam,
                    limit: props?.limit || 10,
                });
            return {
                data: response.data,
                meta: response.meta,
            };
        },
        getNextPageParam: (lastPage) =>
            lastPage.meta.current_page <
            lastPage.meta.last_page
                ? lastPage.meta.current_page + 1
                : undefined,
        initialPageParam: 1,
    });
};

export const useGetBeneficiary = (
    id?: string,
) => {
    const { token } = authStore();

    const api = new UserManagementController();

    return useQuery({
        queryKey:
            QK.user.management.getBeneficiary(
                id!,
            ),
        queryFn: async () => {
            try {
                console.log(
                    "Fetching beneficiary with ID:",
                    id,
                );
                const response =
                    await api.getBeneficiary(id!);
                return response;
            } catch (error) {
                console.error(
                    "Error fetching beneficiary:",
                    error,
                );
                return;
            }
        },
        enabled: !!token && !!id,
    });
};

export const useGetFamilyMembers = (props?: {
    search?: string;
}) => {
    const { token } = authStore();

    const api = new UserManagementController();
    return useQuery({
        queryKey:
            QK.user.management.getFamilyMembers({
                search: props?.search || "",
            }),
        queryFn: async () => {
            const response =
                await api.getFamilyMembers();
            return response;
        },
        enabled: !!token,
    });
};

export const useGetFamilyMember = (
    id?: string,
) => {
    const { token } = authStore();
    if (!token || !id) {
        throw new Error("No token found");
    }

    const api = new UserManagementController();

    return useQuery({
        queryKey:
            QK.user.management.getFamilyMember(
                id,
            ),
        queryFn: async () => {
            const response =
                await api.getFamilyMember(id);
            return response;
        },
        enabled: !!token,
    });
};

export const useGetCareWorkers = (props?: {
    search?: string;
}) => {
    const { token } = authStore();
    if (!token) {
        throw new Error("No token found");
    }

    const api = new UserManagementController();
    return useQuery({
        queryKey: [
            QK.user.management.getCareWorkers,
            props?.search,
        ],
        queryFn: async () => {
            const response =
                await api.getCareWorkers({
                    search: props?.search,
                });
            return response;
        },
        enabled: !!token,
    });
};

export const useGetCareWorker = (id?: string) => {
    const { token } = authStore();
    if (!token || !id) {
        throw new Error("No token found");
    }

    const api = new UserManagementController();

    return useQuery({
        queryKey:
            QK.user.management.getCareWorker(id),
        queryFn: async () => {
            const response =
                await api.getCareWorker(id);
            return response;
        },
        enabled: !!token,
    });
};

export const useGetCareManagers = (props?: {
    search?: string;
}) => {
    const { token } = authStore();
    if (!token) {
        throw new Error("No token found");
    }

    const api = new UserManagementController();
    return useQuery({
        queryKey: [
            QK.user.management.getCareManagers,
            token,
            props?.search,
        ],
        queryFn: async () => {
            const response =
                await api.getCareManagers({
                    search: props?.search,
                });
            return response;
        },
        enabled: !!token,
    });
};

export const useGetCareManager = (
    id?: string,
) => {
    const { token } = authStore();
    if (!token || !id) {
        throw new Error("No token found");
    }

    const api = new UserManagementController();

    return useQuery({
        queryKey:
            QK.user.management.getCareManager(id),
        queryFn: async () => {
            const response =
                await api.getCareManager(id);
            console.log(
                "Care Manager Response:",
                response,
            );
            return response;
        },
        enabled: !!token,
    });
};

export const useGetAdministrators = (props?: {
    search?: string;
}) => {
    const { token } = authStore();
    if (!token) {
        throw new Error("No token found");
    }

    const api = new UserManagementController();
    return useQuery({
        queryKey: [
            QK.user.management.getAdministrators,
            token,
            props?.search,
        ],
        queryFn: async () => {
            const response =
                await api.getAdministrators({
                    search: props?.search,
                });
            return response;
        },
        enabled: !!token,
    });
};

export const useGetAdmin = (id?: string) => {
    const { token } = authStore();
    if (!token || !id) {
        throw new Error("No token found");
    }

    const api = new UserManagementController();

    return useQuery({
        queryKey: QK.user.management.getAdmin(id),
        queryFn: async () => {
            const response =
                await api.getAdmin(id);
            return response;
        },
        enabled: !!token,
    });
};
