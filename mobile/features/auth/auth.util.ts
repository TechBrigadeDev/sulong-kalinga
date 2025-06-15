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

export const isPortal = () => {
    const currentRole = authStore().role;

    if (!currentRole) {
        return false;
    }

    return (
        [...(staffRoles as string[])].includes(
            currentRole,
        ) === false
    );
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

export const roleLabel = (
    role: IRole,
): string => {
    switch (role) {
        case "beneficiary":
            return "Beneficiary";
        case "family_member":
            return "Family Member";
        case "admin":
            return "Admin";
        case "care_manager":
            return "Care Manager";
        case "care_worker":
            return "Care Worker";
        default:
            return role;
    }
};
