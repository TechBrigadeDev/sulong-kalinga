import { z } from "zod";

import { faqSchema } from "./schema";

export type I_FAQ = z.infer<typeof faqSchema>;
