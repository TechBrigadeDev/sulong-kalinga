import { z } from "zod";

export const familyPortalSchema = z.object({
    id: z.number(),
    first_name: z.string(),
    last_name: z.string(),
    mobile: z.string().nullable(),
    email: z
        .string()
        .email()
        .nullable()
        .optional(),
    username: z.string().nullable().optional(),
    landline: z.string().nullable(),
    street_address: z.string().nullable(),
    photo: z.string().nullable(),
    photo_url: z.string().nullable(),
});
