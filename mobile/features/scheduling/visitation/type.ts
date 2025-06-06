import { z } from "zod";

import { visitationSchema } from "./schema";

export type IVisitation = z.infer<
    typeof visitationSchema
>;
