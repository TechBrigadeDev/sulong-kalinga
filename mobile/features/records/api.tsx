import { Controller } from "common/api";
import { log } from "common/debug";

import {
    reportsResponseSchema,
    wcpRecordResponseSchema,
    wcpRecordsResponseSchema,
} from "./schema";

class ReportController extends Controller {
    async getReports(params?: {
        search?: string;
        page?: number;
        limit?: number;
    }) {
        const queryParams = new URLSearchParams();

        if (params?.search) {
            queryParams.append(
                "search",
                params.search,
            );
        }
        if (params?.page) {
            queryParams.append(
                "page",
                params.page.toString(),
            );
        }
        if (params?.limit) {
            queryParams.append(
                "limit",
                params.limit.toString(),
            );
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

    async getWCPRecords(params?: {
        search?: string;
        page?: number;
        limit?: number;
    }) {
        const queryParams = new URLSearchParams();

        if (params?.search) {
            queryParams.append(
                "search",
                params.search,
            );
        }
        if (params?.page) {
            queryParams.append(
                "page",
                params.page.toString(),
            );
        }
        if (params?.limit) {
            queryParams.append(
                "limit",
                params.limit.toString(),
            );
        }

        const response = await this.api.get(
            "/records/weekly-care-plans",
            {
                params: queryParams.toString()
                    ? queryParams
                    : undefined,
            },
        );
        const data = await response.data;

        const validate =
            await wcpRecordsResponseSchema.safeParseAsync(
                data,
            );

        if (!validate.success) {
            console.error(
                "Invalid WCP response data:",
                validate.error,
            );
            throw new Error(
                "Invalid WCP response data",
            );
        }

        return validate.data;
    }

    async getWCPRecord(id: string) {
        const response = await this.api.get(
            `/records/weekly-care-plans/${id}`,
        );
        const data = await response.data;

        const validate =
            await wcpRecordResponseSchema.safeParseAsync(
                data,
            );

        if (!validate.success) {
            log(
                "Invalid WCP record data:",
                JSON.stringify(data, null, 2),
                validate.error,
            );
            throw new Error(
                "Invalid WCP record data",
            );
        }

        log("Fetched WCP record:", validate.data);

        return validate.data;
    }
}

export const reportsController =
    new ReportController();
