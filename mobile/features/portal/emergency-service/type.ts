import { z } from "zod";

import { emergencyServiceRequestSchema } from "./schema";

export type IEmergencyServiceRequest = z.infer<
    typeof emergencyServiceRequestSchema
>;

export type ICurrentEmergencyServiceForm =
    | "emergency"
    | "service";
