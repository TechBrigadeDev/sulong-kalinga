import { ReceiptPoundSterlingIcon } from "lucide-react-native";
import { z } from "zod";

export const beneficiarySchema = z.object({
    beneficiary_id: z.number(),
    first_name: z.string(),
    last_name: z.string(),
    barangay_id: z.number(),
    beneficiary_status_id: z.number(),
    birthday: z.string(),
    beneficiary_signature: z.string().nullable(),
    care_service_agreement_doc: z.string().nullable(),
    care_worker_signature: z.string().nullable(),
    category_id: z.number(),
    civil_status: z.string(),
    created_at: z.string(),
    created_by: z.number(),
    emergency_contact_email: z.string(),
    emergency_contact_mobile: z.string(),
    emergency_contact_name: z.string(),
    emergency_contact_relation: z.string(),
    emergency_procedure: z.string(),
    gender: z.string(),
    general_care_plan_doc: z.string().nullable(),
    general_care_plan_id: z.number(),
    landline: z.string(),
    mobile: z.string(),
    municipality_id: z.number(),
    photo: z.string().nullable(),
    portal_account_id: z.number(),
    remember_token: z.string().nullable(),
    status_reason: z.string(),
    street_address: z.string(),
    updated_at: z.string(),
    updated_by: z.number(),
})
export type IBeneficiary = z.infer<typeof beneficiarySchema>; 

export const familyMemberSchema = z.object({
    family_member_id: z.number(),
    first_name: z.string(),
    last_name: z.string(),
    email: z.string().email(),
    mobile: z.string(),
    relation_to_beneficiary: z.string(),
    is_primary_caregiver: z.boolean(),
    photo: z.string().nullable(),
    photo_url: z.string().nullable(),
    beneficiary: beneficiarySchema,
})
export type IFamilyMember = z.infer<typeof familyMemberSchema>;

export const careWorkerSchema = z.object({
    id: z.number(),
    email: z.string().email(),
    email_verified_at: z.string().nullable(),
    created_at: z.string(),
    updated_at: z.string(),
    first_name: z.string(),
    last_name: z.string(),
    birthday: z.string(),
    civil_status: z.string(),
    educational_background: z.string(),
    mobile: z.string(),
    landline: z.string(),
    personal_email: z.string().email(),
    address: z.string(),
    gender: z.string(),
    religion: z.string(),
    nationality: z.string(),
    volunteer_status: z.string(),
    status_start_date: z.string(),
    status_end_date: z.string(),
    role_id: z.number(),
    status: z.enum(["Active", "Inactive"]),
    organization_role_id: z.number().nullable(),
    assigned_municipality_id: z.number(),
    assigned_care_manager_id: z.number(),
    photo: z.string().nullable(),
    government_issued_id: z.string().nullable(),
    sss_id_number: z.string(),
    philhealth_id_number: z.string(),
    pagibig_id_number: z.string(),
    cv_resume: z.string().nullable(),
    updated_by: z.number(),
    municipality: z.object({
        municipality_id: z.number(),
        municipality_name: z.string(),
        province_id: z.number(),
        created_at: z.string(),
        updated_at: z.string(),
    }),
    photo_url: z.string().nullable(),
    government_issued_id_url: z.string().nullable(),
    cv_resume_url: z.string().nullable(),
})
export type ICareWorker = z.infer<typeof careWorkerSchema>;

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
    photo: z.string().nullable(),
    role: z.enum(["admin", "care_manager", "care_worker"]),
    status: userStatusSchema,
})
export type IUser = z.infer<typeof userSchema>;