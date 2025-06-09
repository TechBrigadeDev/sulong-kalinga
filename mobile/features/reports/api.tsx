import { Controller } from "common/api";

import { reportsResponseSchema } from "./schema";

class ReportController extends Controller {
    async getReports() {
        const response =
            await this.api.get("/reports");
        const data = await response.data;

        const validate =
            await reportsResponseSchema.safeParseAsync(
                data,
            );

        if (!validate.success) {
            console.error(
                "Invalid response data:",
                validate.error,
            );
            throw new Error(
                "Invalid response data",
            );
        }

        return validate.data;
    }
}

export const reportsController =
    new ReportController();
