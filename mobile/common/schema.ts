import { z } from "zod";

export const listResponseSchema = <
    T extends z.ZodTypeAny,
>(
    itemSchema: T,
) =>
    z.object({
        success: z.boolean().optional(),
        data: z.array(itemSchema),
    });

export const itemResponseSchema = <
    T extends z.ZodTypeAny,
>(
    itemSchema: T,
) =>
    z.object({
        success: z.boolean(),
        data: itemSchema,
    });

export const messageResponseSchema = (
    message: string,
) =>
    z.object({
        success: z.boolean(),
        message: z
            .string()
            .refine(
                (msg) => msg.includes(message),
                {
                    message: `Message must contain "${message}"`,
                },
            ),
        data: z.any().optional(),
    });
