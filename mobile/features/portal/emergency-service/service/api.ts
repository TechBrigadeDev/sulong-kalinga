import { Controller } from "common/api";
import { log } from "common/debug";
import { IRole } from "features/auth/auth.interface";
import { portalPath } from "features/auth/auth.util";
import { z } from "zod";

import { serviceTypes } from "./schema";

class ServiceController extends Controller {
    async getServiceTypes(role: IRole) {
        try {
            const path = portalPath(
                role,
                "/emergency-service/service/types",
            );

            const response =
                await this.api.get(path);

            const validate = await z
                .object({
                    success: z.boolean(),
                    data: z.array(serviceTypes),
                })
                .parseAsync(response.data);

            if (!validate.success) {
                throw new Error(
                    "Invalid response data",
                );
            }

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

const serviceController = new ServiceController();
export default serviceController;
