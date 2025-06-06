import { useInfiniteQuery } from "@tanstack/react-query";
import { QK } from "common/query";
import { authStore } from "features/auth/auth.store";

import { medicationSchedulingController } from "./medication.api";

const api = medicationSchedulingController;

export const useMedicationSchedule = (props?: {
    search?: string;
    limit?: number;
}) => {
    const { token } = authStore();

    return useInfiniteQuery({
        queryKey: [
            QK.scheduling.medication.getSchedules,
            token,
            props?.search,
        ],
        queryFn: async ({ pageParam = 1 }) => {
            const response =
                await api.getSchedules({
                    search: props?.search,
                    page: pageParam,
                    limit: props?.limit || 15,
                });
            return {
                data: response.data.data,
                meta: {
                    current_page:
                        response.data
                            .current_page,
                    last_page:
                        response.data.last_page,
                    per_page:
                        response.data.per_page,
                    total: response.data.total,
                },
            };
        },
        getNextPageParam: (lastPage) =>
            lastPage.meta.current_page <
            lastPage.meta.last_page
                ? lastPage.meta.current_page + 1
                : undefined,
        initialPageParam: 1,
        enabled: !!token,
    });
};
