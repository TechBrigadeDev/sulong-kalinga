import { z } from "zod";

const userNotificationSchema = z.enum([
    "family_member",
    "beneficiary",
    "cose_staff",
]);

export const notificationSchema = z.object({
    notification_id: z.number(),
    user_id: z.number(),
    user_type: userNotificationSchema,
    message_title: z.string(),
    message: z.string(),
    date_created: z.string(),
    is_read: z.boolean(),
    created_at: z.string(),
    updated_at: z.string(),
});
