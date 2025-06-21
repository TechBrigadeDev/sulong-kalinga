import { z } from "zod";

import {
    interventionCategorySchema,
    interventionSchema,
} from "./schema";

export type IInterventionCategory = z.infer<
    typeof interventionCategorySchema
>;

export type IIntervention = z.infer<
    typeof interventionSchema
>;

export type IInterventions = Record<
    IInterventionCategory,
    IIntervention[]
>;
