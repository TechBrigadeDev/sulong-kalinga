import { z } from "zod";

export const listResponseSchema = <
    T extends z.ZodTypeAny,
>(
    itemSchema: T,
) =>
    z.object({
        success: z.boolean(),
        data: z.array(itemSchema),
    });
