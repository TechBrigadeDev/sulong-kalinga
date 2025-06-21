import { z } from "zod";

export const reportTypeSchema = z.enum([
    "Weekly Care Plan",
]);

export const reportSchema = z.object({
    id: z.number().nullable(),
    report_id: z.string().nullable(),
    report_type: reportTypeSchema,
    author_id: z.number().nullable(),
    author_first_name: z.string().nullable(),
    author_last_name: z.string().nullable(),
    beneficiary_id: z.number(),
    beneficiary_first_name: z.string(),
    beneficiary_last_name: z.string(),
    created_at: z.string(),
    updated_at: z.string(),
    notes: z.string().nullable(),
    summary: z.string().nullable(),
});

export const reportsResponseSchema = z.object({
    success: z.boolean(),
    reports: z.array(reportSchema),
    pagination: z.object({
        total: z.number(),
        per_page: z.number(),
        current_page: z.number(),
        last_page: z.number(),
    }),
});

export const wcpRecordsSchema = z.object({
    id: z.number(),
    date: z.string(),
    beneficiary: z.string(),
    care_worker: z.string(),
    assessment: z.string().optional(),
    photo_url: z.string(),
});

export const wcpRecordsResponseSchema = z.object({
    success: z.boolean(),
    data: z.array(wcpRecordsSchema),
    meta: z.object({
        current_page: z.number(),
        last_page: z.number(),
        per_page: z.number(),
        total: z.number(),
    }),
});

const wcpBeneficiarySchema = z.object({
    full_name: z.string(),
    address: z.string(),
    medical_conditions: z.string().nullable(),
    illnesses: z.array(z.string()).nullable(),
    civil_status: z.string(),
});

export const wcpRecordSchema = z.object({
    id: z.number(),
    date: z.string(),
    beneficiary: wcpBeneficiarySchema,
    care_worker: z.string(),
    assessment: z.string().optional(),
    evaluation_recommendations: z
        .string()
        .optional(),
    illnesses: z.array(z.string()).optional(),
    vital_signs: z.object({
        vital_signs_id: z.number(),
        blood_pressure: z.string(),
        body_temperature: z.string(),
        pulse_rate: z.number(),
        respiratory_rate: z.number(),
        created_by: z.number(),
        created_at: z.string(),
        updated_at: z.string(),
    }),
    interventions: z.array(
        z.object({
            wcp_intervention_id: z.number(),
            weekly_care_plan_id: z.number(),
            intervention_id: z
                .number()
                .nullable(),
            care_category_id: z
                .number()
                .nullable(),
            intervention_description: z
                .string()
                .nullable(),
            duration_minutes: z.string(),
            implemented: z.boolean(),
        }),
    ),
    photo_url: z.string(),
    created_at: z.string(),
    updated_at: z.string(),
    acknowledge_status: z.string(),
    who_acknowledged: z.string().nullable(),
});

export const wcpRecordResponseSchema = z.object({
    success: z.boolean(),
    data: wcpRecordSchema,
});
