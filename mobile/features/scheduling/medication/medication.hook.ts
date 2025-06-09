import {
    useInfiniteQuery,
    useQuery,
} from "@tanstack/react-query";
import { QK } from "common/query";
import { authStore } from "features/auth/auth.store";

import { medicationScheduleListStore } from "./list/store";
import { medicationSchedulingController } from "./medication.api";

const api = medicationSchedulingController;

export const useMedicationSchedules = (props?: {
    search?: string;
    limit?: number;
}) => {
    const { token } = authStore();
    const { status } =
        medicationScheduleListStore();

    return useInfiniteQuery({
        queryKey: [
            QK.scheduling.medication.getSchedules,
            token,
            props?.search,
            status || "",
        ],
        queryFn: async ({ pageParam = 1 }) => {
            const response =
                await api.getSchedules({
                    search: props?.search,
                    page: pageParam,
                    limit: props?.limit || 10,
                    status: status || undefined,
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

export const useMedicationSchedule = (
    id: string,
) => {
    const { token } = authStore();
    return useQuery({
        queryKey:
            QK.scheduling.medication.getSchedule(
                id,
            ),
        queryFn: async () => {
            const response =
                await api.getSchedule(id);
            return response.data;
        },
        enabled: !!token && !!id,
    });
};
