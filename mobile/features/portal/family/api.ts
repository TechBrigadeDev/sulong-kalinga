import { Controller } from "common/api";
import { log } from "common/debug";
import { IRole } from "features/auth/auth.interface";
import { portalPath } from "features/auth/auth.util";
import { z } from "zod";

import { familyPortalSchema } from "./schema";

class FamilyPortalController extends Controller {
    async getFamilyMembers(role: IRole) {
        const path = portalPath(
            role,
            "/relatives",
        );

        const response = await this.api.get(path);

        const validate = await z
            .object({
                type: z.enum(["beneficiary"]),
                family_members: z.array(
                    familyPortalSchema,
                ),
            })
            .safeParseAsync(response.data);

        if (!validate.success) {
            log(
                JSON.stringify(
                    {
                        error: validate.error,
                        response: response.data,
                    },
                    null,
                    2,
                ),
                "familyPortalController.getFamilyMembers",
            );
            throw new Error(
                "Invalid response data for family members",
            );
        }

        console.log(
            "Family members data validated successfully",
            validate.data.family_members,
        );
        return validate.data.family_members;
    }
}

const familyPortalController =
    new FamilyPortalController();
export default familyPortalController;
