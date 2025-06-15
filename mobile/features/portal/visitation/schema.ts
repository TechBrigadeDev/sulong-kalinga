import { z } from "zod";

export const statusSchema = z.enum([
    "scheduled",
    "completed",
    "canceled",
]);

export const visitationSchema = z.object({
    visitation_id: z.number(),
    occurrence_id: z.number(),
    occurrence_date: z
        .string()
        .datetime({ offset: true }),
    start_time: z.string().nullable(),
    end_time: z.string().nullable(),
    status: statusSchema,
    is_modified: z.boolean(),
    notes: z.string().nullable(),
    created_at: z
        .string()
        .datetime({ offset: true }),
    updated_at: z
        .string()
        .datetime({ offset: true }),
});
