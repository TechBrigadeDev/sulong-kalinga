import { z } from "zod";

import { emergencyAssistanceFormSchema } from "./schema";

export type IEmergencyForm = z.infer<
    typeof emergencyAssistanceFormSchema
>;
