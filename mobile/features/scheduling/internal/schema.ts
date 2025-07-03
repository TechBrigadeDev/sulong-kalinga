import { z } from "zod";

import { appointmentTypeSchema } from "./_schema/appointment-type.schema";
import {
    appointmentStatusSchema,
    occurrenceSchema,
} from "./_schema/occurrence.schema";
import { participantSchema } from "./_schema/participant.schema";

export const internalAppointmentSchema = z.object(
    {
        appointment_id: z.number(),
        appointment_type_id: z.number(),
        title: z.string(),
        description: z.string().nullable(),
        other_type_details: z.string().nullable(),
        date: z.string().datetime(),
        start_time: z
            .string()
            .datetime()
            .nullable(),
        end_time: z
            .string()
            .datetime()
            .nullable(),
        is_flexible_time: z.boolean(),
        meeting_location: z.string(),
        status: appointmentStatusSchema,
        notes: z.string().nullable(),
        created_by: z.number(),
        updated_by: z.number().nullable(),
        created_at: z.string().datetime(),
        updated_at: z.string().datetime(),
        appointment_type: appointmentTypeSchema,
        participants: z.array(participantSchema),
        occurrences: z.array(occurrenceSchema),
    },
);

// Response schemas
export const internalAppointmentsResponseSchema =
    z.object({
        success: z.boolean(),
        data: z.array(
            internalAppointmentSchema.omit({
                participants: true,
            }),
        ),
    });

export const internalAppointmentResponseSchema =
    z.object({
        success: z.boolean(),
        data: internalAppointmentSchema,
    });

export { appointmentTypeSchema } from "./_schema/appointment-type.schema";
export {
    appointmentStatusSchema,
    occurrenceSchema,
} from "./_schema/occurrence.schema";
export {
    participantSchema,
    participantTypeSchema,
} from "./_schema/participant.schema";
