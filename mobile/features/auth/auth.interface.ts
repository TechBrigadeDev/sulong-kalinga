import { z } from "zod";

import {
    rolesEnum,
    userSchema,
} from "./auth.schema";

export type IRole = z.infer<typeof rolesEnum>;

export type IUser = z.infer<typeof userSchema>;
