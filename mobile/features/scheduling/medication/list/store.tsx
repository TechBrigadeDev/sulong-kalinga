import { create } from "zustand";

interface State {
    search: string;
    setSearch: (search: string) => void;
}

export const medicationScheduleListStore =
    create<State>((set) => ({
        search: "",
        setSearch: (search: string) =>
            set({ search }),
    }));
