import { z } from "zod";
import { updateEmailSchema, userProfileSchema } from "./user.schema";


export type IUserProfile = z.infer<typeof userProfileSchema>;

export type IEmailUpdate = z.infer<typeof updateEmailSchema>;