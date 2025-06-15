import { z } from "zod";

import { familyPortalSchema } from "./schema";

export type IFamilyMember = z.infer<
    typeof familyPortalSchema
>;
