import { useQuery } from "@tanstack/react-query";
import { QK } from "common/query";
import { authStore } from "features/auth/auth.store";

import { visitationController } from "./api";

export const useVisitations = () => {
    const { token } = authStore();

    return useQuery({
        queryKey: [
            QK.scheduling.visitation
                .getVisitations,
        ],
        queryFn: async () => {
            const { data } =
                await visitationController.getSchedules();
            return data;
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
