import { Controller } from "common/api";
import { log } from "common/debug";
import { listResponseSchema } from "common/schema";
import { IRole } from "features/auth/auth.interface";
import { portalPath } from "features/auth/auth.util";

import { faqSchema } from "./schema";

class FAQController extends Controller {
    async getFAQs(role: IRole) {
        const path = portalPath(role, "/faq");

        const response = await this.api.get(path);
        const validate = await listResponseSchema(
            faqSchema,
        ).safeParseAsync(response.data);

        if (!validate.success) {
            log(
                "FAQController.getFAQs",
                validate.error,
                response.data,
            );
            throw new Error(
                "Failed to validate FAQ response",
            );
        }

        return validate.data.data;
    }
}

const faqController = new FAQController();
export default faqController;
