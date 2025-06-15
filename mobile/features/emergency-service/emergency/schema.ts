import { z } from "zod";

export const emergencyTypeSchema = z.object({
    emergency_type_id: z.number(),
    name: z.string(),
    color_code: z.string(),
    description: z.string().nullable(),
    created_at: z.string().datetime(),
    updated_at: z.string().datetime(),
});

export const submitEmergencySchema = z.object({
    message: z.string().min(1, {
        message: "Message is required",
    }),
});
