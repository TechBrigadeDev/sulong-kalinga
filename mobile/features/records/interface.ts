import { z } from "zod";

import { wcpRecordSchema } from "./schema";

export type IRecordDetail = z.infer<
    typeof wcpRecordSchema
>;
