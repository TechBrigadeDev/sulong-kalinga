import { Controller } from "common/api";

import { reportsResponseSchema } from "./schema";

class ReportController extends Controller {
    async getReports(params?: {
        search?: string;
        page?: number;
        limit?: number;
    }) {
        const queryParams = new URLSearchParams();
        
        if (params?.search) {
            queryParams.append("search", params.search);
        }
        if (params?.page) {
            queryParams.append("page", params.page.toString());
        }
        if (params?.limit) {
            queryParams.append("limit", params.limit.toString());
        }

        const url = `/reports${queryParams.toString() ? `?${queryParams.toString()}` : ""}`;
        const response = await this.api.get(url);
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
