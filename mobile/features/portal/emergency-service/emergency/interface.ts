import { Ref } from "react";
import { TamaguiElement } from "tamagui";
import { z } from "zod";

import { emergencyTypeSchema } from "./schema";

export type IEmergencyType = z.infer<
    typeof emergencyTypeSchema
>;

export interface EmergencyServiceFormProp {
    ref: Ref<TamaguiElement>;
}
