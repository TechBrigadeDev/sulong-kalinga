import { z } from "zod";

import { serviceAssistanceFormSchema } from "./schema";

export type IServiceForm = z.infer<
    typeof serviceAssistanceFormSchema
>;
