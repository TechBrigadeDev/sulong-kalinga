import { z } from "zod";

export const beneficiaryFormSchema = z.object({
    // Personal Details Section
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

    // Address Section
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

    // Medical History Section
    medical_conditions: z.string().optional(),

    medications: z.string().optional(),

    allergies: z.string().optional(),

    immunizations: z.string().optional(),

    // Care Needs Section
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

    // Medication Section
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
        .optional()
        .default([]),

    // Cognitive Function Section
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

    // Emergency Contact Section
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

    // Documents Section
    photo: z.string().optional(),

    care_service_agreement_doc: z
        .string()
        .optional(),

    general_care_plan_doc: z.string().optional(),

    beneficiary_signature: z.string().optional(),

    care_worker_signature: z.string().optional(),
});

export type IBeneficiaryForm = z.infer<
    typeof beneficiaryFormSchema
>;

export const beneficiaryFormDefaults: IBeneficiaryForm =
    {
        // Personal Details
        first_name: "",
        last_name: "",
        birthday: "",
        gender: "",
        civil_status: "",
        primary_caregiver: "",
        mobile: "",

        // Address
        street_address: "",
        municipality_id: undefined,
        barangay_id: undefined,

        // Medical History
        medical_conditions: "",
        medications: "",
        allergies: "",
        immunizations: "",

        // Care Needs
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

        // Medications
        medications_list: [],

        // Cognitive Function
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

        // Emergency Contact
        emergency_contact_name: "",
        emergency_contact_relation: "",
        emergency_contact_mobile: "",
        emergency_procedure: "",

        // Documents
        photo: "",
        care_service_agreement_doc: "",
        general_care_plan_doc: "",
        beneficiary_signature: "",
        care_worker_signature: "",
    };
