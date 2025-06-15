import { medicationScheduleSchema } from "features/portal/medication/medication";
import { z } from "zod";

export type MedicationSchedule = z.infer<
    typeof medicationScheduleSchema
>;
