import { z } from "zod";

export const emergencyServiceRequestListSchema =
    z.object({
        type: z.string(),
        id: z.number(),
        description: z.string(),
        date_submitted: z.string().datetime(),
        status: z.string(),
        assigned_to: z.string().nullable(),
        actions: z.array(z.string()),
    });
