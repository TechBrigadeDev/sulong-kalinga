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

export const paginatedResponseSchema = <
    T extends z.ZodTypeAny,
>(
    itemSchema: T,
) =>
    z.object({
        success: z.boolean(),
        data: z.object({
            current_page: z.number(),
            data: z.array(itemSchema),
            first_page_url: z.string(),
            from: z.number().nullable(),
            last_page: z.number(),
            last_page_url: z.string(),
            links: z.array(
                z.object({
                    url: z.string().nullable(),
                    label: z.string(),
                    active: z.boolean(),
                }),
            ),
            next_page_url: z.string().nullable(),
            path: z.string(),
            per_page: z.number(),
            prev_page_url: z.string().nullable(),
            to: z.number().nullable(),
            total: z.number(),
        }),
    });

export interface IPagenatedResponse<T> {
    current_page: number;
    data: T[];
    first_page_url: string;
    from: number;
    last_page: number;
    last_page_url: string;
    links: {
        url: string | null;
        label: string;
        active: boolean;
    }[];
    next_page_url: string | null;
    path: string;
    per_page: number;
    prev_page_url: string | null;
    to: number;
    total: number;
    meta: {
        current_page: number;
        last_page: number;
        total: number;
        per_page: number;
    };
}
