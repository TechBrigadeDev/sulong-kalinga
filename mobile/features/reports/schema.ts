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
