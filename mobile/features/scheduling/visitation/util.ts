import { IVisitType } from "./type";

export const visitTypeLabel = (
    visitType: IVisitType,
) => {
    switch (visitType) {
        case "routine_care_visit":
            return "Routine Care Visit";
        case "service_request":
            return "Service Request";
        case "emergency_visit":
            return "Emergency Visit";
        default:
            return "Unknown Visit Type";
    }
};
