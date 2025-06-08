import { z } from "zod";

import {
    visitationSchema,
    visitTypeSchema,
} from "./schema";

export type IVisitation = z.infer<
    typeof visitationSchema
>;

export type IVisitType = z.infer<
    typeof visitTypeSchema
>;
