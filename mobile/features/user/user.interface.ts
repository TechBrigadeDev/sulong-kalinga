import { z } from "zod";

import {
    staffProfileSchema,
    updateEmailSchema,
    updatePasswordSchema,
    userProfileSchema,
} from "./user.schema";

export type IUserProfile = z.infer<
    typeof userProfileSchema
>;

export type dtoEmailUpdate = z.infer<
    typeof updateEmailSchema
>;

export type dtoUpdatePassword = z.infer<
    typeof updatePasswordSchema
>;

export type IStaffProfile = z.infer<
    typeof staffProfileSchema
>;
