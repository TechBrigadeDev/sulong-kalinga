import { Href } from "expo-router";
import { IRole } from "features/auth/auth.interface";
import { icons } from "lucide-react-native";
import { GetThemeValueForKey } from "tamagui";

const portalRoles: IRole[] = [
    "beneficiary",
    "family_member",
];

const staffRoles: IRole[] = [
    "admin",
    "care_manager",
    "care_worker",
];

export const portalMenuItems: IMenuItem[] = [
    {
        name: "visitations",
        title: "Visitations",
        href: "/(tabs)/(portal)/visitation",
        color: "#1d0086",
        icon: "Calendar",
        permissions: portalRoles,
    },
    {
        name: "medication",
        title: "Medication",
        href: "/(tabs)/(portal)/medication",
        color: "#0222FF",
        icon: "Pill",
        permissions: portalRoles,
    },
    {
        name: "emergency-service",
        title: "Emergency & Service",
        href: "/(tabs)/(portal)/emergency-service",
        color: "#ff0202",
        icon: "ClipboardPlus",
        permissions: portalRoles,
    },
    {
        name: "care-plan",
        title: "Care Plan",
        href: "/(tabs)/(portal)/care-plan",
        color: "#1B8000",
        icon: "FileText",
        permissions: portalRoles,
    },
    {
        name: "family-members",
        title: "Family Members",
        href: "/(tabs)/(portal)/family",
        color: "#800080",
        icon: "Users",
        permissions: portalRoles,
    },
    {
        name: "faq",
        title: "FAQ",
        href: "/(tabs)/(portal)/faq",
        color: "#FF8C00",
        icon: "CircleHelp",
        permissions: portalRoles,
    },
];

export const staffMenuItems: IMenuItem[] = [
    {
        name: "medication",
        title: "Medication",
        href: "/scheduling/medication",
        color: "#0222FF",
        icon: "Pill",
        permissions: staffRoles,
    },
    {
        name: "visitations",
        title: "Visitations",
        href: "/scheduling/visitations",
        color: "#FF0000",
        icon: "Calendar",
        permissions: staffRoles,
    },
    {
        name: "emergency-service",
        title: "Shifts",
        href: "/(tabs)/shifts",
        color: "#FCA500",
        icon: "Clock",
        permissions: staffRoles,
    },
    {
        name: "care-plan",
        title: "Care Plan",
        href: "/options/reports/care-records",
        color: "#1B8000",
        icon: "FileText",
        permissions: staffRoles,
    },
    {
        name: "internal-appointments",
        title: "Internal Appointments",
        href: "/scheduling/internal",
        color: "#800080",
        icon: "Users",
        permissions: staffRoles,
    },
];

export interface IMenuItem {
    name: string;
    title: string;
    href: Href;
    color: GetThemeValueForKey<"backgroundColor">;
    icon: keyof typeof icons;
    permissions: IRole[];
}
