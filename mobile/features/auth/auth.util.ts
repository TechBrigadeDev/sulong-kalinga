import { staffRoles } from "./auth.constant";
import { IRole } from "./auth.interface";
import { authStore } from "./auth.store";

export const isRoleStaff = (
    role: IRole,
): boolean => {
    return [...(staffRoles as string[])].includes(
        role,
    );
};

export const portalPath = (
    role: IRole,
    path: string = "/",
): string => {
    let basePath = "/";
    if (role === "beneficiary") {
        basePath = "/portal/beneficiary";
    } else if (role === "family_member") {
        basePath = "/portal/family";
    }

    return `${basePath}${path}`.replace(
        /\/\//g,
        "/",
    );
};

export const hasRole = (...roles: IRole[]) => {
    const currentRole = authStore().role;
    if (!currentRole) {
        return false;
    }
    return roles.includes(currentRole);
};

export const isStaff = () => {
    const currentRole = authStore().role;

    if (!currentRole) {
        return false;
    }

    return [...(staffRoles as string[])].includes(
        currentRole,
    );
};
