import { Controller } from "common/api";

import { visitationsResponseSchema } from "./schema";

class VisitationController extends Controller {
    async getSchedules() {
        const response = await this.api.get(
            "/visitations",
        );
        const data = response.data;

        const validate =
            await visitationsResponseSchema.safeParseAsync(
                data,
            );
        if (!validate.success) {
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
