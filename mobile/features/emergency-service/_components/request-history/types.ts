export interface EmergencyRequestHistory {
    id: number;
    type: string;
    description: string;
    date_submitted: string;
    date_resolved?: string;
    status: string;
    assigned_to: string | null;
    handled_by?: string | null;
    resolution_notes?: string;
    actions?: string[];
}
