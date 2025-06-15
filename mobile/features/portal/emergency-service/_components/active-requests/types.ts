export interface EmergencyRequest {
    id: number;
    type: string;
    description: string;
    date_submitted: string;
    status: string;
    assigned_to: string | null;
}

export { default as Badge } from "./Badge";
export { default } from "./index";
export { default as RequestCard } from "./RequestCard";
