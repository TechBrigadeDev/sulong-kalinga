import { Controller } from "common/api";
import { listResponseSchema } from "common/schema";
import { IRole } from "features/auth/auth.interface";
import { portalPath } from "features/auth/auth.util";

import { visitationSchema } from "./schema";

class VisitationController extends Controller {
    async getEvents(role: IRole) {
        try {
            const path = portalPath(
                role,
                "/visitation-schedule/events",
            );

            const response =
                await this.api.get(path);

            const validation =
                await listResponseSchema(
                    visitationSchema,
                ).safeParseAsync(response.data);

            if (!validation.success) {
                throw new Error(
                    `Invalid response data: ${JSON.stringify(
                        validation.error,
                    )}`,
                );
            }

            return validation.data;
        } catch (error) {
            throw error;
        }
    }
}

const visitationController =
    new VisitationController();
export default visitationController;
