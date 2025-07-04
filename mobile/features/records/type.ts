import { z } from "zod";

import {
    reportSchema,
    wcpInterventionSchema,
    wcpRecordSchema,
    wcpRecordsSchema,
} from "./schema";

export type IReport = z.infer<
    typeof reportSchema
>;

export type IWCPRecords = z.infer<
    typeof wcpRecordsSchema
>;

export type IWCPRecord = z.infer<
    typeof wcpRecordSchema
>;

export type IWCPIntervention = z.infer<
    typeof wcpInterventionSchema
>;

export type IBeneficiary = z.infer<
    typeof wcpRecordSchema.shape.beneficiary
>;
