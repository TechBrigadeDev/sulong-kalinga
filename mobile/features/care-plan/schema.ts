import { z } from "zod";

export const interventionCategorySchema = z.enum([
    "Mobility",
    "Cognitive/Communication",
    "Self-sustainability",
    "Disease/Therapy Handling",
    "Daily life/Social contact",
    "Outdoor Activities",
    "Household Keeping",
]);

export const interventionSchema = z.object({
    intervention_id: z.number(),
    intervention_description: z.string(),
});

export const interventionListSchema = z.object({
    care_category_id: z.number(),
    care_category_name:
        interventionCategorySchema,
    interventions: z.array(interventionSchema),
});
