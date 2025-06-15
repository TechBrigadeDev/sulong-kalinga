import { Controller } from "common/api";
import { log } from "common/debug";
import { listResponseSchema } from "common/schema";
import { IRole } from "features/auth/auth.interface";
import { portalPath } from "features/auth/auth.util";

import { medicationScheduleSchema } from "./medication";

class MedicationController extends Controller {
    async getMedications(role: IRole) {
        const path = portalPath(
            role,
            "/medication-schedule",
        );

        const response = await this.api.get(path);

        const validate = await listResponseSchema(
            medicationScheduleSchema,
        ).safeParseAsync(response.data);

        if (!validate.success) {
            log(
                "MedicationController.getMedications",
                validate.error,
            );
            throw new Error(
                "Invalid medication data received",
            );
        }

        return validate.data.data;
    }
}

const medicationController =
    new MedicationController();
export default medicationController;
