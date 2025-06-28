import { z } from "zod";

export const personalDetailsSchema = z.object({
    beneficiaryId: z
        .string()
        .min(1, "Please select a beneficiary"),
    illness: z
        .string()
        .max(
            500,
            "The illnesses list cannot exceed 500 characters",
        )
        .nullable()
        .optional(),
    assessment: z
        .string()
        .min(
            20,
            "Assessment must be at least 20 characters",
        )
        .max(
            5000,
            "Assessment cannot exceed 5000 characters",
        ),
    bloodPressure: z
        .string()
        .regex(
            /^\d{2,3}\/\d{2,3}$/,
            "Blood pressure must be in format 120/80",
        ),
    pulseRate: z
        .number()
        .int("Pulse rate must be an integer")
        .min(
            40,
            "Pulse rate must be between 40 and 200",
        )
        .max(
            200,
            "Pulse rate must be between 40 and 200",
        ),
    temperature: z
        .number()
        .min(
            35,
            "Body temperature must be between 35°C and 42°C",
        )
        .max(
            42,
            "Body temperature must be between 35°C and 42°C",
        ),
    respiratoryRate: z
        .number()
        .int(
            "Respiratory rate must be an integer",
        )
        .min(
            8,
            "Respiratory rate must be between 8 and 40",
        )
        .max(
            40,
            "Respiratory rate must be between 8 and 40",
        ),
});

export const interventionSchema = z.object({
    id: z.string(),
    name: z
        .string()
        .min(1, "Intervention name is required"),
    minutes: z
        .number()
        .min(
            0.01,
            "Duration must be greater than 0",
        )
        .max(
            999.99,
            "Duration cannot exceed 999.99 minutes",
        ),
    isCustom: z.boolean().optional(),
    // For custom interventions
    categoryId: z.string().optional(),
    description: z
        .string()
        .min(
            5,
            "Custom intervention description must be at least 5 characters",
        )
        .max(
            255,
            "Custom intervention description cannot exceed 255 characters",
        )
        .regex(
            /^(?=.*[a-zA-Z])[a-zA-Z0-9\s,.!?;:()\-\'"]+$/,
            "Custom intervention description must contain text and can only include letters, numbers, and basic punctuation",
        )
        .optional(),
});

export const mobilitySchema = z.array(
    interventionSchema,
);

export const cognitiveSchema = z.array(
    interventionSchema,
);

export const selfSustainabilitySchema = z.array(
    interventionSchema,
);

export const diseaseTherapySchema = z.array(
    interventionSchema,
);

export const socialContactSchema = z.array(
    interventionSchema,
);

export const outdoorActivitySchema = z.array(
    interventionSchema,
);

export const householdKeepingSchema = z.array(
    interventionSchema,
);

export const evaluationSchema = z.object({
    pictureUri: z
        .string({
            required_error:
                "A photo is required for documentation purposes",
        })
        .min(
            1,
            "A photo is required for documentation purposes",
        ),
    recommendations: z
        .string()
        .min(
            20,
            "Evaluation and recommendations must be at least 20 characters",
        )
        .max(
            5000,
            "Evaluation and recommendations cannot exceed 5000 characters",
        ),
});

export const carePlanFormSchema = z
    .object({
        personalDetails: personalDetailsSchema,
        mobility: mobilitySchema,
        cognitive: cognitiveSchema,
        selfSustainability:
            selfSustainabilitySchema,
        diseaseTherapy: diseaseTherapySchema,
        socialContact: socialContactSchema,
        outdoorActivity: outdoorActivitySchema,
        householdKeeping: householdKeepingSchema,
        evaluation: evaluationSchema,
    })
    .superRefine((data, ctx) => {
        // Check if at least one intervention exists across all care categories
        const totalInterventions =
            (data.mobility?.length || 0) +
            (data.cognitive?.length || 0) +
            (data.selfSustainability?.length ||
                0) +
            (data.diseaseTherapy?.length || 0) +
            (data.socialContact?.length || 0) +
            (data.outdoorActivity?.length || 0) +
            (data.householdKeeping?.length || 0);

        if (totalInterventions === 0) {
            // Add error to the first care category field (mobility) to show the error message
            ctx.addIssue({
                code: z.ZodIssueCode.custom,
                message:
                    "Please select at least one intervention from any care category",
                path: ["interventions"],
            });
        }
    });
