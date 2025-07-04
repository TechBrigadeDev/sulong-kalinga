import { z } from "zod";

export const adminSchema = z.object({
    id: z.number(),
    first_name: z.string(),
    last_name: z.string(),
    email: z.string().email(),
    personal_email: z.string().nullable(),
    mobile: z.string().optional(),
    photo: z.string().nullable(),
    photo_url: z.string().nullable(),
    created_at: z
        .string()
        .default(() => new Date().toISOString()),
    updated_at: z
        .string()
        .default(() => new Date().toISOString()),
    educational_background: z.string().optional(),
    birthday: z.string().optional(),
    gender: z.string().optional(),
    civil_status: z.string().optional(),
    religion: z.string().optional(),
    nationality: z.string().optional(),
    landline: z.string().optional(),
    address: z.string().optional(),
    sss_id: z.string().optional(),
    philhealth_id: z.string().optional(),
    pagibig_id: z.string().optional(),
});
