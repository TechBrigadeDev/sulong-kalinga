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
    // Additional fields used in the form
    primary_caregiver: z.string().nullable(),
    medical_conditions: z.string().optional(),
    medications: z.string().optional(),
    allergies: z.string().optional(),
    immunizations: z.string().optional(),
    medications_list: z
        .array(
            z.object({
                name: z.string(),
                dosage: z.string(),
                frequency: z.string(),
                instructions: z.string(),
            }),
        )
        .optional(),
    // Mobility fields
    walking_ability: z.string().optional(),
    assistive_devices: z.string().optional(),
    transportation_needs: z.string().optional(),
    // Cognitive fields
    memory: z.string().optional(),
    thinking_skills: z.string().optional(),
    orientation: z.string().optional(),
    behavior: z.string().optional(),
    // Emotional fields
    mood: z.string().optional(),
    social_interactions: z.string().optional(),
    emotional_support_need: z.string().optional(),
    // Care needs fields
    mobility_frequency: z.string().optional(),
    mobility_assistance: z.string().optional(),
    cognitive_frequency: z.string().optional(),
    cognitive_assistance: z.string().optional(),
    self_sustainability_frequency: z
        .string()
        .optional(),
    self_sustainability_assistance: z
        .string()
        .optional(),
    disease_therapy_frequency: z
        .string()
        .optional(),
    disease_therapy_assistance: z
        .string()
        .optional(),
    daily_life_frequency: z.string().optional(),
    daily_life_assistance: z.string().optional(),
    outdoor_frequency: z.string().optional(),
    outdoor_assistance: z.string().optional(),
    household_frequency: z.string().optional(),
    household_assistance: z.string().optional(),
});
