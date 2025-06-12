import { z } from "zod";

import {
    carePlanFormSchema,
    cognitiveSchema,
    diseaseTherapySchema,
    evaluationSchema,
    householdKeepingSchema,
    interventionSchema,
    mobilitySchema,
    outdoorActivitySchema,
    personalDetailsSchema,
    selfSustainabilitySchema,
    socialContactSchema,
} from "./schema";

export type CarePlanFormData = z.infer<
    typeof carePlanFormSchema
>;
export type PersonalDetailsData = z.infer<
    typeof personalDetailsSchema
>;
export type InterventionData = z.infer<
    typeof interventionSchema
>;
export type MobilityData = z.infer<
    typeof mobilitySchema
>;
export type CognitiveData = z.infer<
    typeof cognitiveSchema
>;
export type SelfSustainabilityData = z.infer<
    typeof selfSustainabilitySchema
>;
export type DiseaseTherapyData = z.infer<
    typeof diseaseTherapySchema
>;
export type SocialContactData = z.infer<
    typeof socialContactSchema
>;
export type OutdoorActivityData = z.infer<
    typeof outdoorActivitySchema
>;
export type HouseholdKeepingData = z.infer<
    typeof householdKeepingSchema
>;
export type EvaluationData = z.infer<
    typeof evaluationSchema
>;
