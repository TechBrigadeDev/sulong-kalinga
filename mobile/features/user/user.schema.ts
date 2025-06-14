import { z } from "zod";

// Base schema with common fields
const baseUserProfileSchema = z.object({
    first_name: z.string(),
    last_name: z.string(),
    mobile: z.string().nullable(),
    landline: z.string().nullable(),
    gender: z.string().nullable(),
    birthday: z.string().nullable(),
    photo: z.string().nullable(),
    created_by: z.number().optional(),
    updated_by: z.number().optional(),
    created_at: z.string().optional(),
    updated_at: z.string().optional(),
});

// Extended base for staff members with additional fields
const staffBaseProfileSchema =
    baseUserProfileSchema.extend({
        id: z.number(),
        full_name: z.string(),
        middle_name: z.string().nullable(),
        civil_status: z.string().nullable(),
        educational_background: z
            .string()
            .nullable(),
        religion: z.string().nullable(),
        nationality: z.string().nullable(),
        work_email: z.string().email().nullable(),
        personal_email: z
            .string()
            .email()
            .nullable(),
        address: z.string().nullable(),
        municipality: z.string().nullable(),
        assigned_care_manager: z
            .string()
            .nullable(),
        account_status: z.string().nullable(),
        volunteer_status: z.string().nullable(),
        status_start_date: z.string().nullable(),
        status_end_date: z.string().nullable(),
        sss_id: z.string().nullable(),
        philhealth_id: z.string().nullable(),
        pagibig_id: z.string().nullable(),
        member_since: z.string().nullable(),
        role_id: z.number().nullable(),
        photo_url: z.string().nullable(),
        username: z.string().nullable(),
        email: z.string().email().nullable(),
    });

// Beneficiary profile schema
const beneficiaryProfileSchema =
    baseUserProfileSchema.extend({
        beneficiary_id: z.number(),
        middle_name: z.string().nullable(),
        civil_status: z.string().nullable(),
        primary_caregiver: z.string().nullable(),
        street_address: z.string().nullable(),
        barangay_id: z.number().optional(),
        municipality_id: z.number().optional(),
        category_id: z.number().optional(),
        emergency_contact_name: z
            .string()
            .nullable(),
        emergency_contact_relation: z
            .string()
            .nullable(),
        emergency_contact_mobile: z
            .string()
            .nullable(),
        emergency_contact_email: z
            .string()
            .email()
            .nullable(),
        emergency_procedure: z
            .string()
            .nullable(),
        beneficiary_status_id: z
            .number()
            .optional(),
        status_reason: z.string().nullable(),
        general_care_plan_id: z
            .number()
            .optional(),
        username: z.string(),
        beneficiary_signature: z
            .string()
            .nullable(),
        care_worker_signature: z
            .string()
            .nullable(),
        general_care_plan_doc: z
            .string()
            .nullable(),
        care_service_agreement_doc: z
            .string()
            .nullable(),
        map_location: z.string().nullable(),
        role: z.literal("beneficiary"),
    });

// Family member profile schema
const familyMemberProfileSchema =
    baseUserProfileSchema.extend({
        family_member_id: z.number(),
        email: z.string().email(),
        street_address: z.string().nullable(),
        related_beneficiary_id: z.number(),
        relation_to_beneficiary: z.string(),
        is_primary_caregiver: z.boolean(),
        role: z.literal("family_member"),
    });

// Admin profile schema
const adminProfileSchema =
    staffBaseProfileSchema.extend({
        organization_role_id: z.number(),
        role: z.literal("admin"),
    });

// Care manager profile schema
const careManagerProfileSchema =
    staffBaseProfileSchema.extend({
        organization_role_id: z
            .number()
            .nullable(),
        role: z.literal("care_manager"),
    });

// Care worker profile schema
const careWorkerProfileSchema =
    staffBaseProfileSchema.extend({
        organization_role_id: z.number(),
        role: z.literal("care_worker"),
    });

export const staffProfileSchema = z.union([
    adminProfileSchema,
    careManagerProfileSchema,
    careWorkerProfileSchema,
]);

// Discriminated union for user profiles
export const userProfileSchema =
    z.discriminatedUnion("role", [
        beneficiaryProfileSchema,
        familyMemberProfileSchema,
        adminProfileSchema,
        careManagerProfileSchema,
        careWorkerProfileSchema,
    ]);

export const updateEmailSchema = z.object({
    new_email: z.string().email(),
    password: z.string(),
});

export const updatePasswordSchema = z
    .object({
        current_password: z.string(),
        new_password: z.string().min(8, {
            message:
                "New password must be at least 8 characters long",
        }),
        confirm_password: z.string(),
    })
    .refine(
        (data) =>
            data.new_password ===
            data.confirm_password,
        {
            message:
                "New password and confirm password must match",
        },
    )
    .refine(
        (data) =>
            data.current_password !==
            data.new_password,
        {
            message:
                "New password must be different from current password",
        },
    );
