import { Controller } from "common/api";

import { visitationsResponseSchema } from "./schema";

class VisitationController extends Controller {
    async getSchedules(params?: {
        start_date?: Date;
        end_date?: Date;
    }) {
        const response = await this.api.get(
            "/visitations",
            {
                params: {
                    ...(params?.start_date && {
                        start_date:
                            params.start_date,
                    }),
                    ...(params?.end_date && {
                        end_date: params.end_date,
                    }),
                },
            },
        );
        const data = response.data;

        const validate =
            await visitationsResponseSchema.safeParseAsync(
                data,
            );
        if (!validate.success) {
            console.error(
                "Error validating visitations data",
                validate.error,
            );
            throw new Error(
                "Invalid data format received from the server",
            );
        }

        return validate.data;
    }

    // Define methods for handling care worker schedules
    async getSchedule(workerId: string) {
        // Logic to fetch the schedule for a specific care worker
    }
}

export const visitationController =
    new VisitationController();
