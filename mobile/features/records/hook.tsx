import { useInfiniteQuery } from "@tanstack/react-query";
import { QK } from "common/query";
import { authStore } from "features/auth/auth.store";

import { reportsController } from "./api";

const api = reportsController;

export const useCarePlans = (props?: {
    search?: string;
    limit?: number;
}) => {
    const { token } = authStore((state) => state);

    return useInfiniteQuery({
        queryKey: [QK.report.getReports, props?.search],
        queryFn: async ({ pageParam = 1 }) => {
            const response = await api.getReports({
                search: props?.search,
                page: pageParam,
                limit: props?.limit || 10,
            });
            return {
                data: response.reports,
                meta: {
                    current_page: response.pagination.current_page,
                    last_page: response.pagination.last_page,
                    total: response.pagination.total,
                    per_page: response.pagination.per_page,
                },
            };
        },
        getNextPageParam: (lastPage) =>
            lastPage.meta.current_page < lastPage.meta.last_page
                ? lastPage.meta.current_page + 1
                : undefined,
        initialPageParam: 1,
        enabled: !!token,
    });
};

export const useWCPRecords = (props?: {
    search?: string;
    limit?: number;
}) => {
    const { token } = authStore((state) => state);

    return useInfiniteQuery({
        queryKey: [QK.report.getWCPRecords, props?.search],
        queryFn: async ({ pageParam = 1 }) => {
            const response = await api.getWCPRecords({
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
            lastPage.meta.current_page < lastPage.meta.last_page
                ? lastPage.meta.current_page + 1
                : undefined,
        initialPageParam: 1,
        enabled: !!token,
    });
}