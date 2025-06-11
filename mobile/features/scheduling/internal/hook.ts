import { useQuery } from "@tanstack/react-query";
import { isSameDay } from "common/date";
import { QK } from "common/query";
import { useToast } from "common/toast";
import { authStore } from "features/auth/auth.store";

import { internalSchedulingController } from "./api";
import { internalScheduleListStore } from "./list/store";

const api = internalSchedulingController;

export const useInternalSchedules = () => {
    const { token } = authStore();
    const {
        currentDate,
        startOfWeek,
        endOfWeek,
    } = internalScheduleListStore();
    const toast = useToast();

    return useQuery({
        queryKey: [
            QK.scheduling.internal.getSchedules,
            startOfWeek.toDateString(),
            endOfWeek.toDateString(),
        ],
        queryFn: async () => {
            try {
                const response =
                    await api.getSchedules({
                        start_date: startOfWeek
                            .toISOString()
                            .split("T")[0],
                        end_date: endOfWeek
                            .toISOString()
                            .split("T")[0],
                    });

                // Filter appointments based on current date and occurrences
                return response
                    .filter((item) => {
                        const appointmentDate =
                            new Date(item.date);
                        const occurrenceDates =
                            item.occurrences.map(
                                (occurrence) =>
                                    occurrence.occurrence_date,
                            );

                        return (
                            occurrenceDates.some(
                                (date) =>
                                    isSameDay(
                                        date,
                                        currentDate,
                                    ),
                            ) ||
                            isSameDay(
                                appointmentDate,
                                currentDate,
                            )
                        );
                    })
                    .sort(
                        (a, b) =>
                            new Date(
                                a.date,
                            ).getTime() -
                            new Date(
                                b.date,
                            ).getTime(),
                    );
            } catch (error) {
                console.error(
                    "Error fetching internal schedules:",
                    error,
                );
                toast.error(
                    "Failed to fetch internal schedules. Please try again later.",
                );
                return [];
            }
        },
        enabled: !!token,
    });
};

export const useInternalSchedule = (
    appointmentId: string,
) => {
    const { token } = authStore();

    return useQuery({
        queryKey: [
            QK.scheduling.internal.getSchedule,
            appointmentId,
        ],
        queryFn: async () => {
            const response =
                await api.getSchedule(
                    appointmentId,
                );
            return response;
        },
        enabled: !!appointmentId && !!token,
    });
};
