import { z } from "zod";

// Status enum for appointments and occurrences
export const appointmentStatusSchema = z.enum([
    "scheduled",
    "completed",
    "canceled",
]);

export const occurrenceSchema = z.object({
    occurrence_id: z.number(),
    appointment_id: z.number(),
    occurrence_date: z.string().datetime(),
    start_time: z.string().datetime().nullable(),
    end_time: z.string().datetime().nullable(),
    status: appointmentStatusSchema,
    is_modified: z.boolean(),
    notes: z.string().nullable(),
    created_at: z.string().datetime(),
    updated_at: z.string().datetime(),
});

export type IAppointmentStatus = z.infer<
    typeof appointmentStatusSchema
>;
export type IOccurrence = z.infer<
    typeof occurrenceSchema
>;
