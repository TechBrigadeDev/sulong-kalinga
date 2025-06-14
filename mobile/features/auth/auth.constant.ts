import { rolesEnum } from "./auth.schema";

export const staffRoles = [
    rolesEnum.enum.admin,
    rolesEnum.enum.care_manager,
    rolesEnum.enum.care_worker,
];

export const roles = rolesEnum.enum;
