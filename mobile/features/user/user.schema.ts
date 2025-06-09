import { z } from "zod";

export const userStatusSchema = z.enum([
    "Active",
    "Inactive",
]);
export type IUserStatus = z.infer<
    typeof userStatusSchema
>;

export const userRoleSchema = z.enum([
    "admin",
    "care_manager",
    "care_worker",
    "portal",
]);
export type IUserRole = z.infer<
    typeof userRoleSchema
>;

export const userSchema = z.object({
    email: z.string(),
    first_name: z.string(),
    id: z.number(),
    last_name: z.string(),
    mobile: z.string(),
    photo_url: z.string().nullable(),
    role: z.enum([
        "admin",
        "care_manager",
        "care_worker",
    ]),
    // status: userStatusSchema,
});
export type IUser = z.infer<typeof userSchema>;

export const userProfileSchema = z.object({
    id: z.number(),
    first_name: z.string(),
    last_name: z.string(),
    full_name: z.string(),
    middle_name: z.string().nullable(),
    birthday: z.string().nullable(),
    civil_status: z.string().nullable(),
    educational_background: z.string().nullable(),
    gender: z.string().nullable(),
    religion: z.string().nullable(),
    nationality: z.string().nullable(),
    work_email: z.string().email().nullable(),
    personal_email: z.string().email().nullable(),
    mobile: z.string().nullable(),
    landline: z.string().nullable(),
    address: z.string().nullable(),
    municipality: z.string().nullable(),
    assigned_care_manager: z.string().nullable(),
    account_status: z.string().nullable(),
    volunteer_status: z.string().nullable(),
    status_start_date: z.string().nullable(),
    status_end_date: z.string().nullable(),
    sss_id: z.string().nullable(),
    philhealth_id: z.string().nullable(),
    pagibig_id: z.string().nullable(),
    member_since: z.string().nullable(),
    role: z.string().nullable(),
    role_id: z.number().nullable(),
    organization_role_id: z.number().nullable(),
    photo_url: z.string().nullable(),
    username: z.string().nullable(),
    email: z.string().email().nullable()
});


export const updateEmailSchema = z.object({
    new_email: z.string().email(),
    password: z.string(),
});