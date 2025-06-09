import { z } from "zod";

import { reportSchema, wcpRecordSchema } from "./schema";

export type IReport = z.infer<
    typeof reportSchema
>;

export type IWCPRecord = z.infer<
    typeof wcpRecordSchema
>;
