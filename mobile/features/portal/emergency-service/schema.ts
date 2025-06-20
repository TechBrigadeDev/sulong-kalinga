import { z } from "zod";

const requestTypeSchema = z.enum([
    "emergency",
    "service",
]);

const baseRequestSchema = z.object({
    id: z.number(),
    type: requestTypeSchema,
    description: z.string(),
    date_submitted: z.string().datetime(),
    status: z.string(),
    assigned_to: z.string().nullable(),
});

const emergencyTypeSchema =
    baseRequestSchema.extend({
        type: z.literal(
            requestTypeSchema.enum.emergency,
        ),
        emergency_type_id: z.number(),
    });

const serviceTypeSchema =
    baseRequestSchema.extend({
        type: z.literal(
            requestTypeSchema.enum.service,
        ),
        service_date: z.string().datetime(),
        service_time: z.string(),
        service_type_id: z.number(),
    });

export const emergencyServiceRequestSchema =
    z.union([
        emergencyTypeSchema,
        serviceTypeSchema,
    ]);

export const emergencyServiceHistorySchema =
    z.union([
        emergencyTypeSchema.omit({
            emergency_type_id: true,
        }),
        serviceTypeSchema.omit({
            service_type_id: true,
        }),
    ]);
