import { z } from "zod";

import {
    groupedMedicationScheduleSchema,
    medicationScheduleSchema,
} from "./medication.schema";

export type IMedicationSchedule = z.infer<
    typeof medicationScheduleSchema
>;

export type IGroupedMedicationSchedule = z.infer<
    typeof groupedMedicationScheduleSchema
>;
