import { z } from 'zod';

export const loginSchema = {
    response: z.object({
        success: z.boolean(),
        token: z.string(),
    })
}