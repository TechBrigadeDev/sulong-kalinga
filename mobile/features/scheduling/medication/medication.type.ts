import { z } from "zod";

import {
    groupedMedicationScheduleSchema,
    medicationScheduleSchema,
    medicationScheduleStatusEnum,
} from "./medication.schema";

export type IMedicationSchedule = z.infer<
    typeof medicationScheduleSchema
>;

export type IGroupedMedicationSchedule = z.infer<
    typeof groupedMedicationScheduleSchema
>;

export type IMedicationScheduleStatus = z.infer<
    typeof medicationScheduleStatusEnum
>;
