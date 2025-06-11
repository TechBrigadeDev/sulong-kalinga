import { z } from "zod";

export const appointmentTypeSchema = z.object({
    appointment_type_id: z.number(),
    // name: z.string(),
    color_code: z.string(),
    description: z.string(),
    created_at: z.string().nullable(),
    updated_at: z.string().nullable(),
});

export type IAppointmentType = z.infer<
    typeof appointmentTypeSchema
>;
