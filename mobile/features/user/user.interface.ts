import { z } from "zod";
import { userProfileSchema } from "./user.schema";


export type IUserProfile = z.infer<typeof userProfileSchema>;