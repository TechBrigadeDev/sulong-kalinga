import { z } from "zod";

import {
    statusSchema,
    visitationSchema,
} from "./schema";

export type IVisitation = z.infer<
    typeof visitationSchema
>;
export type IStatus = z.infer<
    typeof statusSchema
>;
