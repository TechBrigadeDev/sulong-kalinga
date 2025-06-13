import { z } from "zod";

export const personalDetailsSchema = z.object({
    first_name: z
        .string()
        .min(1, "First name is required")
        .min(
            2,
            "First name must be at least 2 characters",
        )
        .max(
            50,
            "First name must be less than 50 characters",
        ),

    last_name: z
        .string()
        .min(1, "Last name is required")
        .min(
            2,
            "Last name must be at least 2 characters",
        )
        .max(
            50,
            "Last name must be less than 50 characters",
        ),

    birthday: z
        .string()
        .min(1, "Birthday is required")
        .regex(
            /^\d{4}-\d{2}-\d{2}$/,
            "Birthday must be in YYYY-MM-DD format",
        ),

    gender: z
        .string()
        .min(1, "Gender is required")
        .refine(
            (val) =>
                [
                    "Male",
                    "Female",
                    "Other",
                ].includes(val),
            {
                message:
                    "Gender must be Male, Female, or Other",
            },
        ),

    civil_status: z
        .string()
        .min(1, "Civil status is required")
        .refine(
            (val) =>
                [
                    "Single",
                    "Married",
                    "Widowed",
                    "Divorced",
                ].includes(val),
            {
                message:
                    "Civil status must be Single, Married, Widowed, or Divorced",
            },
        ),

    primary_caregiver: z
        .string()
        .optional()
        .nullable(),

    mobile: z
        .string()
        .min(1, "Mobile number is required")
        .regex(
            /^\+63\d{10}$/,
            "Mobile number must be in +63XXXXXXXXXX format",
        ),
});

export const addressSchema = z.object({
    street_address: z
        .string()
        .min(1, "Street address is required")
        .max(
            255,
            "Street address must be less than 255 characters",
        ),

    municipality_id: z
        .number()
        .min(1, "Municipality is required")
        .optional()
        .nullable(),

    barangay_id: z
        .number()
        .min(1, "Barangay is required")
        .optional()
        .nullable(),
});

export const medicalHistorySchema = z.object({
    medical_conditions: z.string().optional(),
    medications: z.string().optional(),
    allergies: z.string().optional(),
    immunizations: z.string().optional(),
});

export const careNeedsSchema = z.object({
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

export const medicationSchema = z.object({
    medications_list: z
        .array(
            z.object({
                name: z
                    .string()
                    .min(
                        1,
                        "Medication name is required",
                    ),
                dosage: z
                    .string()
                    .min(1, "Dosage is required"),
                frequency: z
                    .string()
                    .min(
                        1,
                        "Frequency is required",
                    ),
                instructions: z
                    .string()
                    .optional(),
            }),
        )
        .default([]),
});

export const cognitiveFunctionSchema = z.object({
    walking_ability: z.string().optional(),
    assistive_devices: z.string().optional(),
    transportation_needs: z.string().optional(),
    memory: z.string().optional(),
    thinking_skills: z.string().optional(),
    orientation: z.string().optional(),
    behavior: z.string().optional(),
    mood: z.string().optional(),
    social_interactions: z.string().optional(),
    emotional_support_need: z.string().optional(),
});

export const emergencyContactSchema = z.object({
    emergency_contact_name: z.string().optional(),
    emergency_contact_relation: z
        .string()
        .optional(),
    emergency_contact_mobile: z
        .string()
        .optional()
        .refine(
            (val) =>
                !val || /^\+63\d{10}$/.test(val),
            {
                message:
                    "Emergency contact mobile must be in +63XXXXXXXXXX format",
            },
        ),
    emergency_procedure: z.string().optional(),
});

export const documentsSchema = z.object({
    photo: z.string().optional(),
    care_service_agreement_doc: z
        .string()
        .optional(),
    general_care_plan_doc: z.string().optional(),
    beneficiary_signature: z.string().optional(),
    care_worker_signature: z.string().optional(),
});

export const beneficiaryFormSchema =
    personalDetailsSchema
        .merge(addressSchema)
        .merge(medicalHistorySchema)
        .merge(careNeedsSchema)
        .merge(medicationSchema)
        .merge(cognitiveFunctionSchema)
        .merge(emergencyContactSchema)
        .merge(documentsSchema);

export type IBeneficiaryForm = z.infer<
    typeof beneficiaryFormSchema
>;

export type BeneficiaryFormValues = z.infer<
    typeof beneficiaryFormSchema
>;

export const beneficiaryFormDefaults: BeneficiaryFormValues =
    {
        first_name: "",
        last_name: "",
        birthday: "",
        gender: "",
        civil_status: "",
        primary_caregiver: "",
        mobile: "",
        street_address: "",
        municipality_id: undefined,
        barangay_id: undefined,
        medical_conditions: "",
        medications: "",
        allergies: "",
        immunizations: "",
        mobility_frequency: "",
        mobility_assistance: "",
        cognitive_frequency: "",
        cognitive_assistance: "",
        self_sustainability_frequency: "",
        self_sustainability_assistance: "",
        disease_therapy_frequency: "",
        disease_therapy_assistance: "",
        daily_life_frequency: "",
        daily_life_assistance: "",
        outdoor_frequency: "",
        outdoor_assistance: "",
        household_frequency: "",
        household_assistance: "",
        medications_list: [],
        walking_ability: "",
        assistive_devices: "",
        transportation_needs: "",
        memory: "",
        thinking_skills: "",
        orientation: "",
        behavior: "",
        mood: "",
        social_interactions: "",
        emotional_support_need: "",
        emergency_contact_name: "",
        emergency_contact_relation: "",
        emergency_contact_mobile: "",
        emergency_procedure: "",
        photo: "",
        care_service_agreement_doc: "",
        general_care_plan_doc: "",
        beneficiary_signature: "",
        care_worker_signature: "",
    };
