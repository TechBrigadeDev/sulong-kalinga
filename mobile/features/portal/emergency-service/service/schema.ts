import { z } from "zod";

export const serviceTypes = z.object({
    service_type_id: z.number(),
    name: z.string(),
    color_code: z.string().optional(),
    description: z.string().optional(),
    created_at: z.string().datetime(),
    updated_at: z.string().datetime(),
});
