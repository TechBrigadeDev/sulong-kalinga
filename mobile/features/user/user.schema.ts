import { z } from "zod";

export const userStatusSchema = z.enum(["Active", "Inactive"]);
export type IUserStatus = z.infer<typeof userStatusSchema>;

export const userRoleSchema = z.enum(["admin", "care_manager", "care_worker", "portal"]);
export type IUserRole = z.infer<typeof userRoleSchema>;

export const userSchema = z.object({
    email: z.string(),
    first_name: z.string(),
    id: z.number(),
    last_name: z.string(),
    mobile: z.string(),
    photo_url: z.string().nullable(),
    role: z.enum(["admin", "care_manager", "care_worker"]),
    // status: userStatusSchema,
})
export type IUser = z.infer<typeof userSchema>;