import { AxiosInstance } from "axios";
import { axiosClient } from "common/api";
import { authStore } from "features/auth/auth.store";

import { medicationScheduleResponseSchema, medicationSchedulesResponseSchema } from "./medication.schema";

class MedicationSchedulingController {
    constructor(
        private api: AxiosInstance = axiosClient,
    ) {
        this.api = api;
        this.api.defaults.headers.common[
            "Accept"
        ] = "application/json";
        this.api.defaults.headers.common[
            "Content-Type"
        ] = "application/json";
        authStore.subscribe((state) => {
            if (state.token) {
                this.api.defaults.headers.common[
                    "Authorization"
                ] = `Bearer ${state.token}`;
            } else {
                delete this.api.defaults.headers
                    .common["Authorization"];
            }
        });
    }

    async getSchedules(params?: {
        search?: string;
        page?: number;
        limit?: number;
    }) {
        try {
            const response = await this.api.get(
                "/medication-schedules",
                {
                    params: {
                        ...(params?.search && {
                            search: params.search,
                        }),
                        ...(params?.page && {
                            page: params.page,
                        }),
                        ...(params?.limit && {
                            limit: params.limit,
                        }),
                    },
                },
            );
            const data = response.data;

            const validate =
                await medicationSchedulesResponseSchema.safeParseAsync(
                    data,
                );
            if (!validate.success) {
                console.error(
                    "Validation failed:",
                    validate.error,
                );
                throw new Error(
                    "Invalid response data",
                );
            }

            return validate.data;
        } catch (error) {
            console.error(
                "Error fetching medications:",
                error,
            );
            throw error;
        }
    }

    async getSchedule(id: string) {
        try {
            const response = await this.api.get(
                `/medication-schedules/${id}`,
            );
            const data = response.data;

            const validate =
                await medicationScheduleResponseSchema.safeParseAsync(
                    data,
                );
            if (!validate.success) {
                console.error(
                    "Validation failed:",
                    validate.error,
                );
                throw new Error(
                    "Invalid response data",
                );
            }

            return validate.data;
        } catch (error) {
            console.error(
                "Error fetching medication schedule:",
                error,
            );
            throw error;
        }
    }
}

export const medicationSchedulingController =
    new MedicationSchedulingController();
