import { Controller } from "common/api";
import { listResponseSchema } from "common/schema";
import { IRole } from "features/auth/auth.interface";
import { portalPath } from "features/auth/auth.util";

import {
    emergencyServiceHistorySchema,
    emergencyServiceRequestSchema,
} from "./schema";

class EmergencyServicenController extends Controller {
    async getActiveRequests(role: IRole) {
        const path = portalPath(
            role,
            "/emergency-service/active",
        );
        try {
            const response =
                await this.api.get(path);

            const validate =
                await listResponseSchema(
                    emergencyServiceRequestSchema,
                ).safeParseAsync(response.data);

            if (!validate.success) {
                console.error(
                    "Invalid response data",
                    validate.error,
                    response.data,
                );
                throw new Error(
                    "Invalid response data",
                );
            }

            console.log(
                "Active emergency requests fetched successfully",
                response.data,
            );
            return validate.data;
        } catch (error) {
            console.error(
                "Error fetching active emergency requests:",
                error,
            );
            throw new Error(
                "Failed to fetch active emergency requests",
            );
        }
    }

    async getRequestsHistory(role: IRole) {
        const path = portalPath(
            role,
            "/emergency-service/history",
        );
        try {
            const response =
                await this.api.post(path);

            const validate =
                await listResponseSchema(
                    emergencyServiceHistorySchema,
                ).safeParseAsync(response.data);

            if (!validate.success) {
                console.error(
                    "Invalid response data",
                    validate.error,
                    response.data,
                );
                throw new Error(
                    "Invalid response data",
                );
            }

            return validate.data;
        } catch (error) {
            console.error(
                "Error fetching emergency requests history:",
                error,
            );
            throw new Error(
                "Failed to fetch emergency requests history",
            );
        }
    }
}

const emergencyServiceController =
    new EmergencyServicenController();
export default emergencyServiceController;
