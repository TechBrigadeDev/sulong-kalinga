import { z } from "zod";

export const beneficiarySchema = z.object({
    beneficiary_id: z.number(),
    first_name: z.string(),
    last_name: z.string(),
    barangay_id: z.number(),
    beneficiary_status_id: z.number(),
    birthday: z.string(),
    beneficiary_signature: z.string().nullable(),
    care_service_agreement_doc: z
        .string()
        .nullable(),
    care_worker_signature: z.string().nullable(),
    category_id: z.number(),
    civil_status: z.string(),
    created_at: z.string(),
    created_by: z.number(),
    emergency_contact_mobile: z.string(),
    emergency_contact_name: z.string(),
    emergency_contact_relation: z.string(),
    emergency_procedure: z.string(),
    gender: z.string(),
    general_care_plan_doc: z.string().nullable(),
    general_care_plan_id: z.number(),
    mobile: z.string(),
    municipality_id: z.number(),
    photo: z.string().nullable(),
    street_address: z.string(),
    updated_at: z.string(),
    updated_by: z.number(),
});
