import { Controller } from "common/api";
import { log } from "common/debug";
import { IRole } from "features/auth/auth.interface";
import { portalPath } from "features/auth/auth.util";
import { z } from "zod";

import { emergencyTypeSchema } from "./schema";

class EmergencyController extends Controller {
    async getEmergencyTypes(role: IRole) {
        try {
            const path = portalPath(
                role,
                "/emergency-service/emergency/types",
            );

            const response =
                await this.api.get(path);

            const validate = await z
                .object({
                    success: z.boolean(),
                    data: z.array(
                        emergencyTypeSchema,
                    ),
                })
                .parseAsync(response.data);

            if (!validate.success) {
                throw new Error(
                    "Invalid response data",
                );
            }

            log(
                JSON.stringify(
                    validate.data,
                    null,
                    2,
                ),
                "Emergency types fetched successfully",
            );
            return validate.data;
        } catch (error) {
            log(
                "Error fetching emergency types:",
                error,
            );
            throw new Error(
                "Failed to fetch emergency types",
            );
        }
    }
}

const emergencyController =
    new EmergencyController();
export default emergencyController;
