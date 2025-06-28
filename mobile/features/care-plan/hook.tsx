import { useMutation } from "@tanstack/react-query";
import { authStore } from "features/auth/auth.store";

import { carePlanController } from ".";
import { CarePlanFormData } from "./form/type";

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
