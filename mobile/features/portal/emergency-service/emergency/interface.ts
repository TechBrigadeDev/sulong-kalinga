import { z } from "zod";

import { emergencyTypeSchema } from "./schema";

export type IEmergencyType = z.infer<
    typeof emergencyTypeSchema
>;

export interface EmergencyServiceFormProp {
    onSubmitSuccess: () => Promise<void>;
}
