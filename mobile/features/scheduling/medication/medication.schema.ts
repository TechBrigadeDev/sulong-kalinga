import { z } from "zod";

import { beneficiarySchema } from "~/features/user-management/schema/beneficiary";

export const medicationScheduleTypeEnum = z.enum([
    "tablet",
    "inhaler",
    "liquid",
    "injection",
    "patch",
    "cream",
    "drops",
    "other",
    "capsule",
]);

export const medicationScheduleStatusEnum =
    z.enum([
        "active",
        "completed",
        "paused",
        "discontinued",
    ]);

const healthHistorySchema = z.object({
    health_history_id: z.number(),
    general_care_plan_id: z.number(),
    medical_conditions: z.string(),
    medications: z.string(),
    allergies: z.string(),
    immunizations: z.string(),
    formatted_conditions: z.string(),
    formatted_immunizations: z.string(),
    formatted_allergies: z.string(),
});

const generalCarePlanSchema = z.object({
    general_care_plan_id: z.number(),
    care_worker_id: z.number(),
    emergency_plan: z.string(),
    review_date: z.string(),
    created_at: z.string(),
    updated_at: z.string(),
    health_history: healthHistorySchema,
});

export const medicationScheduleSchema = z.object({
    medication_schedule_id: z.number(),
    beneficiary_id: z.number(),
    medication_name: z.string(),
    dosage: z.string(),
    medication_type: medicationScheduleTypeEnum,
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
    updated_at: z.string(),
    beneficiary: beneficiarySchema.extend({
        general_care_plan: generalCarePlanSchema,
    }),
});

export const groupedMedicationScheduleSchema =
    z.object({
        beneficiary: beneficiarySchema,
        medication_schedules: z.array(
            medicationScheduleSchema,
        ),
    });

const paginationLinkSchema = z.object({
    url: z.string().nullable(),
    label: z.string(),
    active: z.boolean(),
});

export const medicationSchedulesResponseSchema =
    z.object({
        success: z.boolean(),
        data: z.object({
            current_page: z.number(),
            data: z.array(
                medicationScheduleSchema,
            ),
            first_page_url: z.string(),
            from: z.number().nullable(),
            last_page: z.number(),
            last_page_url: z.string(),
            links: z.array(paginationLinkSchema),
            next_page_url: z.string().nullable(),
            path: z.string(),
            per_page: z.number(),
            prev_page_url: z.string().nullable(),
            to: z.number().nullable(),
            total: z.number(),
        }),
    });

export const medicationScheduleResponseSchema =
    z.object({
        success: z.boolean(),
        data: medicationScheduleSchema,
    });
