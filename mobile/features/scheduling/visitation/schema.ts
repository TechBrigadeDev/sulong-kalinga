import { beneficiarySchema } from "features/user-management/schema/beneficiary";
import { careWorkerSchema } from "features/user-management/schema/care-worker";
import { z } from "zod";

export const statusSchema = z.enum([
    "scheduled",
    "completed",
    "canceled",
]);

export const occurrenceSchema = z.object({
    occurrence_id: z.number(),
    visitation_id: z.number(),
    occurrence_date: z.string().datetime(),
    start_time: z.string().datetime().nullable(),
    end_time: z.string().datetime().nullable(),
    status: statusSchema,
    is_modified: z.boolean(),
    notes: z.string().nullable(),
    created_at: z.string().datetime(),
    updated_at: z.string().datetime(),
});

export const visitationSchema = z.object({
    visitation_id: z.number(),
    care_worker_id: z.number(),
    beneficiary_id: z.number(),
    visit_type: z.string(),
    visitation_date: z.string().datetime(),
    is_flexible_time: z.boolean(),
    start_time: z.string().datetime().nullable(),
    end_time: z.string().datetime().nullable(),
    notes: z.string().nullable(),
    date_assigned: z.string().datetime(),
    assigned_by: z.number(),
    status: statusSchema,
    confirmed_by_beneficiary: z
        .number()
        .nullable(),
    confirmed_by_family: z.number().nullable(),
    confirmed_on: z
        .string()
        .datetime()
        .nullable(),
    work_shift_id: z.number().nullable(),
    visit_log_id: z.number().nullable(),
    created_at: z.string().datetime(),
    updated_at: z.string().datetime(),
    beneficiary: beneficiarySchema,
    care_worker: careWorkerSchema,
    occurrences: z.array(occurrenceSchema),
});

export const visitationsResponseSchema = z.object(
    {
        success: z.boolean(),
        data: z.array(visitationSchema),
    },
);
