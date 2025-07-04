import {
    AxiosError,
    HttpStatusCode,
} from "axios";
import { Controller } from "common/api";
import { log } from "common/debug";
import { listResponseSchema } from "common/schema";
import { toastServerError } from "common/toast";
import { showToastable } from "react-native-toastable";

import { mapCarePlanFormToApiData } from "./form/mapper";
import { CarePlanFormData } from "./form/type";
import { interventionListSchema } from "./schema";

class CarePlanController extends Controller {
    async postCarePlan(data: CarePlanFormData) {
        try {
            // Map form data to API format
            const apiData =
                await mapCarePlanFormToApiData(
                    data,
                );

            // For FormData uploads in React Native, we should not set Content-Type
            // React Native will handle this automatically with the correct boundary
            const response = await this.api.post(
                "/weekly-care-plans",
                apiData,
                {
                    headers: {
                        "Content-Type":
                            "multipart/form-data",
                    },
                },
            );

            return response.data;
        } catch (error) {
            if (error instanceof AxiosError) {
                switch (error.response?.status) {
                    case HttpStatusCode.Forbidden:
                        showToastable({
                            message:
                                error.response
                                    .data
                                    .message ||
                                "You are not authorized to perform this action.",
                            status: "danger",
                            duration: 4000,
                        });

                    default:
                        toastServerError({
                            data: JSON.stringify(
                                data,
                                null,
                                2,
                            ),
                            error: error.response
                                ?.data,
                            status: error.response
                                ?.status,
                        });

                        break;
                }
                return;
            }
            console.error(
                "Error submitting care plan form:",
                error,
            );

            throw error;
        }
    }

    async getInterventions() {
        try {
            const response = await this.api.get(
                "/interventions/by-category",
            );

            const validate =
                await listResponseSchema(
                    interventionListSchema,
                ).safeParseAsync(response.data);

            if (!validate.success) {
                console.error(
                    "Invalid response data for interventions:",
                    validate.error,
                );
                throw new Error(
                    "Invalid response data for interventions",
                );
            }

            return validate.data.data;
        } catch (error) {
            if (error instanceof AxiosError) {
                switch (error.status) {
                    case HttpStatusCode.BadRequest:
                    default:
                        toastServerError({
                            error: error.response
                                ?.data,
                            status: error.response
                                ?.status,
                        });
                }
            } else {
                console.error(
                    "Error fetching interventions:",
                    error,
                );
            }
            throw error;
        }
    }

    async patchCarePlan(
        id: string,
        data: CarePlanFormData,
    ) {
        if (!id) {
            throw new Error(
                "ID is required to patch a care plan",
            );
        }

        console.log(
            "Patching care plan with ID:",
            id,
        );
        console.log("Data to patch:", data);

        try {
            const apiData =
                await mapCarePlanFormToApiData(
                    data,
                );

            const response = await this.api.patch(
                `/records/weekly-care-plans/${id}`,
                apiData,
                {
                    headers: {
                        "Content-Type":
                            "multipart/form-data",
                    },
                },
            );

            log(
                "Care plan patched successfully:",
                response.status,
                response.data,
            );

            return response.data;
        } catch (error) {
            log(
                "Error patching care plan:",
                error,
            );
            if (error instanceof AxiosError) {
                switch (error.response?.status) {
                    case HttpStatusCode.Forbidden:
                        showToastable({
                            message:
                                error.response
                                    .data
                                    .message ||
                                "You are not authorized to perform this action.",
                            status: "danger",
                            duration: 4000,
                        });
                        break;
                    case HttpStatusCode.NotFound:
                        console.log(
                            "Care plan not found:",
                            error.response?.data,
                        );
                        showToastable({
                            message:
                                "Care plan not found.",
                            status: "danger",
                            duration: 4000,
                        });
                        break;
                    default:
                        toastServerError({
                            data: JSON.stringify(
                                data,
                                null,
                                2,
                            ),
                            error: error.response
                                ?.data,
                            status: error.response
                                ?.status,
                        });
                }
            }
            throw error;
        }
    }
}

export const carePlanController =
    new CarePlanController();
