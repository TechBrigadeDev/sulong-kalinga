import { z } from "zod";

import { emergencyServiceRequestListSchema } from "./schema";

export type IEmergencyServiceRequest = z.infer<
    typeof emergencyServiceRequestListSchema
>;

export type ICurrentEmergencyServiceForm =
    | "emergency"
    | "service";
