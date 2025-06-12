import { useMutation } from "@tanstack/react-query";
import { authStore } from "features/auth/auth.store";

import { carePlanController } from ".";
import { CarePlanFormData } from "./form/type";

export const useSubmitCarePlanForm = () => {
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
            console.error(
                "Error submitting care plan form:",
                error,
            );
        },
    });
};
