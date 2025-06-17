import { beneficiarySchema } from "features/user-management/schema/beneficiary";
import { careWorkerSchema } from "features/user-management/schema/care-worker";
import { z } from "zod";

export const portalCarePlanListSchema = z.object({
    id: z.number(),
    author_name: z.string(),
    acknowledged: z.number().nullable(),
    date: z.string(),
    status: z.string(),
});

const authorSchema = z.object({
    id: z.number(),
    email: z.string(),
    email_verified_at: z.string().nullable(),
    created_at: z.string(),
    updated_at: z.string(),
    first_name: z.string(),
    last_name: z.string(),
    birthday: z.string().nullable(),
    civil_status: z.string().nullable(),
    educational_background: z.string().nullable(),
    mobile: z.string().nullable(),
    landline: z.string().nullable(),
    personal_email: z.string().nullable(),
    address: z.string().nullable(),
    gender: z.string().nullable(),
    religion: z.string().nullable(),
    nationality: z.string().nullable(),
    volunteer_status: z.string().nullable(),
    status_start_date: z.string().nullable(),
    status_end_date: z.string().nullable(),
    role_id: z.number().nullable(),
    status: z.string().nullable(),
    organization_role_id: z.number().nullable(),
    assigned_municipality_id: z
        .number()
        .nullable(),
    assigned_care_manager_id: z
        .number()
        .nullable(),
    photo: z.string().nullable(),
    government_issued_id: z.string().nullable(),
    sss_id_number: z.string().nullable(),
    philhealth_id_number: z.string().nullable(),
    pagibig_id_number: z.string().nullable(),
    cv_resume: z.string().nullable(),
    updated_by: z.number().nullable(),
});

const vitalSignsSchema = z.object({
    vital_signs_id: z.number(),
    blood_pressure: z.string(),
    body_temperature: z.string(),
    pulse_rate: z.number(),
    respiratory_rate: z.number(),
    created_by: z.number(),
    created_at: z.string(),
    updated_at: z.string(),
});

const interventionSchema = z.object({
    wcp_intervention_id: z.number(),
    weekly_care_plan_id: z.number(),
    intervention_id: z.number().nullable(),
    care_category_id: z.number(),
    intervention_description: z
        .string()
        .nullable(),
    duration_minutes: z.string(),
    implemented: z.boolean(),
});

export const portalCarePlanDetailSchema =
    z.object({
        weekly_care_plan_id: z.number(),
        beneficiary_id: z.number(),
        care_worker_id: z.number(),
        vital_signs_id: z.number(),
        date: z.string(),
        assessment: z.string(),
        evaluation_recommendations: z.string(),
        created_by: z.number(),
        updated_by: z.number(),
        acknowledged_by_beneficiary: z
            .number()
            .nullable(),
        acknowledged_by_family: z
            .number()
            .nullable(),
        acknowledgement_signature: z
            .string()
            .nullable(),
        created_at: z.string(),
        updated_at: z.string(),
        assessment_summary_draft: z
            .string()
            .nullable(),
        assessment_translation_draft: z
            .string()
            .nullable(),
        evaluation_summary_draft: z
            .string()
            .nullable(),
        evaluation_translation_draft: z
            .string()
            .nullable(),
        illnesses: z.string(), // JSON string
        photo_path: z.string().nullable(),
        assessment_summary_sections: z
            .any()
            .nullable(), // Adjust based on actual structure
        evaluation_summary_sections: z
            .any()
            .nullable(), // Adjust based on actual structure
        assessment_summary_final: z
            .string()
            .nullable(),
        evaluation_summary_final: z
            .string()
            .nullable(),
        has_ai_summary: z.boolean(),
        assessment_translation_sections: z
            .any()
            .nullable(), // Adjust based on actual structure
        evaluation_translation_sections: z
            .any()
            .nullable(), // Adjust based on actual structure
        // vital_signs: vitalSignsSchema,
        // interventions: z.array(interventionSchema),
        beneficiary: beneficiarySchema,
        author: authorSchema,
        care_worker: careWorkerSchema,
    });
