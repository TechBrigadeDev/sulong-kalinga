import { useQuery } from "@tanstack/react-query";
import { isSameDay } from "common/date";
import { QK } from "common/query";
import { authStore } from "features/auth/auth.store";

import { visitationController } from "./api";
import { visitationListStore } from "./list/store";

export const useVisitations = () => {
    const { token } = authStore();
    const {
        currentDate,
        startOfWeek,
        endOfWeek,
    } = visitationListStore();

    return useQuery({
        queryKey: [
            QK.scheduling.visitation
                .getVisitations,
            startOfWeek.toDateString(),
            endOfWeek.toDateString(),
        ],
        queryFn: async () => {
            const response =
                await visitationController.getSchedules(
                    {
                        start_date: startOfWeek,
                        end_date: endOfWeek,
                    },
                );

            // filter with occurrences date each
            return response.data.filter(
                (item) => {
                    const occurenceDates =
                        item.occurrences.map(
                            (occurrence) =>
                                occurrence.occurrence_date,
                        );
                    return occurenceDates.some(
                        (date) =>
                            isSameDay(
                                date,
                                currentDate,
                            ),
                    );
                },
            );
        },
        // getNextPageParam: (lastPage) => {
        //     if (
        //         lastPage.meta.current_page <
        //         lastPage.meta.last_page
        //     ) {
        //         return (
        //             lastPage.meta.current_page + 1
        //         );
        //     }
        //     return undefined;
        // },
        // initialPageParam: 1,
        enabled: !!token,
    });
};
