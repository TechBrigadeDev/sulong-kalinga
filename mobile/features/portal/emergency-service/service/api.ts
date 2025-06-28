import { Controller } from "common/api";
import { log } from "common/debug";
import { IRole } from "features/auth/auth.interface";
import { portalPath } from "features/auth/auth.util";
import { z } from "zod";

import { IServiceRequestForm } from "./form/schema";
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

    async postServiceRequest(
        role: IRole,
        data: IServiceRequestForm,
    ) {
        try {
            const path = portalPath(
                role,
                "/emergency-service/service/submit",
            );

            const response = await this.api.post(
                path,
                data,
            );

            return response.data;
        } catch (error) {
            log(
                "Error submitting service request:",
                error,
            );
            throw new Error(
                "Failed to submit service request",
            );
        }
    }
}

const serviceController = new ServiceController();
export default serviceController;
