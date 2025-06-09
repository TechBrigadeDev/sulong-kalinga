import { IMedicationScheduleStatus } from "features/scheduling/medication/medication.type";
import { create } from "zustand";

interface State {
    search: string;
    setSearch: (search: string) => void;
    status: IMedicationScheduleStatus | null;
    setStatus: (
        status: IMedicationScheduleStatus | null,
    ) => void;
}

export const medicationScheduleListStore =
    create<State>((set) => ({
        search: "",
        setSearch: (search: string) =>
            set({ search }),
        status: null,
        setStatus: (
            status: IMedicationScheduleStatus | null,
        ) => set({ status }),
    }));
