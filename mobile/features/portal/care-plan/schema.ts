import { z } from "zod";

export const portalCarePlanListSchema = z.object({
    id: z.number(),
    author_name: z.string(),
    acknowledged: z.number().nullable(),
    date: z.string(),
    status: z.string(),
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

const beneficiaryDetailSchema = z.object({
    full_name: z.string(),
    address: z.string(),
    medical_conditions: z.string(),
    illnesses: z.string().nullable(),
    civil_status: z.string(),
});

export const portalCarePlanDetailSchema =
    z.object({
        id: z.number(),
        date: z.string(),
        beneficiary: beneficiaryDetailSchema,
        care_worker: z.string(),
        assessment: z.string(),
        evaluation_recommendations: z.string(),
        illnesses: z.array(z.string()),
        vital_signs: vitalSignsSchema,
        interventions: z.array(
            interventionSchema,
        ),
        photo_url: z.string(),
        created_at: z.string(),
        updated_at: z.string(),
        acknowledge_status: z.string(),
        who_acknowledged: z.string(),
    });

export type Intervention = z.infer<
    typeof interventionSchema
>;
export type VitalSigns = z.infer<
    typeof vitalSignsSchema
>;
