import { Controller } from "common/api";
import { log } from "common/debug";
import {
    itemResponseSchema,
    listResponseSchema,
} from "common/schema";
import { IRole } from "features/auth/auth.interface";
import { portalPath } from "features/auth/auth.util";

import { portalCarePlanDetailSchema, portalCarePlanListSchema } from "./schema";

class CarePlanController extends Controller {
    async getCarePlans(role: IRole) {
        const path = portalPath(
            role,
            "/care-plan",
        );

        const response = await this.api.get(path);

        const validate = await listResponseSchema(
            portalCarePlanListSchema,
        ).safeParseAsync(response.data);

        if (!validate.success) {
            log(
                "CarePlanController.getCarePlans",
                validate.error,
            );
            throw new Error(
                "Invalid response format for care plans",
            );
        }

        return validate.data;
    }

    async getCarePlanById(
        role: IRole,
        id: string,
    ) {
        const path = portalPath(
            role,
            `/care-plan/view/${id}`,
        );

        const response = await this.api.get(path);

        const validate = await itemResponseSchema(
            portalCarePlanDetailSchema,
        ).safeParseAsync(response.data);

        if (!validate.success) {
            log(
                "CarePlanController.getCarePlanById",
                validate.error,
            );
            throw new Error(
                "Invalid response format for care plan",
            );
        }

        return validate.data;
    }
}

const carePlanController =
    new CarePlanController();
export default carePlanController;
