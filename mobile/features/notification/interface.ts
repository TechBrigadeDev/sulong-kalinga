import { z } from "zod";

import { notificationSchema } from "./schema";

export type INotification = z.infer<
    typeof notificationSchema
>;
