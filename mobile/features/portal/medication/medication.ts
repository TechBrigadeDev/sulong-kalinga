import { z } from "zod";

export const medicationScheduleStatusEnum =
    z.enum([
        "active",
        "completed",
        "paused",
        "discontinued",
    ]);

export const medicationScheduleSchema = z.object({
    medication_schedule_id: z.number(),
    beneficiary_id: z.number(),
    medication_name: z.string(),
    dosage: z.string(),
    medication_type: z.string(),
    morning_time: z.string().nullable(),
    noon_time: z.string().nullable(),
    evening_time: z.string().nullable(),
    night_time: z.string().nullable(),
    as_needed: z.boolean(),
    with_food_morning: z.boolean(),
    with_food_noon: z.boolean(),
    with_food_evening: z.boolean(),
    with_food_night: z.boolean(),
    special_instructions: z.string().nullable(),
    start_date: z.string(),
    end_date: z.string().nullable(),
    status: medicationScheduleStatusEnum,
    created_by: z.number(),
    updated_by: z.number().nullable(),
    created_at: z.string(),
    updated_at: z.string().nullable(),
});
