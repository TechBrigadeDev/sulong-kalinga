import {
    useMutation,
    useQuery,
} from "@tanstack/react-query";
import { QK } from "common/query";
import { authStore } from "features/auth/auth.store";

import { carePlanController } from ".";
import { CarePlanFormData } from "./form/type";
import {
    IIntervention,
    IInterventionCategory,
} from "./interface";

export const useSubmitCarePlanForm = (props: {
    onError?: (error: Error) => Promise<void>;
}) => {
    const { token } = authStore();

    if (!token) {
        throw new Error(
            "User is not authenticated",
        );
    }

    return useMutation({
        mutationFn: async (
            data: CarePlanFormData,
        ) => {
            const response =
                await carePlanController.postCarePlan(
                    data,
                );
            return response;
        },
        onError: (error) => {
            if (props.onError) {
                props.onError(error);
            }
        },
    });
};

export const useGetInterventions = () => {
    const { token } = authStore();
    const query = useQuery({
        queryKey: [QK.carePlan.getInterventions],
        queryFn: async () => {
            if (!token) {
                throw new Error(
                    "User is not authenticated",
                );
            }
            return await carePlanController.getInterventions();
        },
        enabled: !!token,
        staleTime: 5 * 60 * 1000, // 5 minutes
        gcTime: 10 * 60 * 1000, // 10 minutes
        refetchOnWindowFocus: false,
        refetchOnReconnect: false,
    });

    const getInterventions = (
        category: IInterventionCategory,
    ) => {
        if (!query.data) {
            return [];
        }

        const interventions = query.data.find(
            (item) =>
                item.care_category_name ===
                category,
        );

        if (!interventions) {
            return [];
        }

        return interventions.interventions as IIntervention[];
    };

    const getCategoryId = (
        category: IInterventionCategory,
    ) => {
        if (!query.data) {
            return undefined;
        }

        const categoryData = query.data.find(
            (item) =>
                item.care_category_name ===
                category,
        );

        return categoryData?.care_category_id;
    };

    const interventions: Record<
        IInterventionCategory,
        IIntervention[]
    > = {
        Mobility: getInterventions("Mobility"),
        "Cognitive/Communication":
            getInterventions(
                "Cognitive/Communication",
            ),
        "Self-sustainability": getInterventions(
            "Self-sustainability",
        ),
        "Disease/Therapy Handling":
            getInterventions(
                "Disease/Therapy Handling",
            ),
        "Daily life/Social contact":
            getInterventions(
                "Daily life/Social contact",
            ),
        "Outdoor Activities": getInterventions(
            "Outdoor Activities",
        ),
        "Household Keeping": getInterventions(
            "Household Keeping",
        ),
    };

    return {
        ...query,
        interventions,
        getCategoryId,
    };
};
