import { z } from "zod";

import { reportSchema } from "./schema";

export type IReport = z.infer<
    typeof reportSchema
>;
