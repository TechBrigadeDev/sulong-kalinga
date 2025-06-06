import { create } from "zustand";

interface State {
    date: Date | undefined;
    setDate: (date: Date | undefined) => void;
}

export const weekCalendarStore = create<State>((set) => ({
    date: new Date(),
    setDate: (date) => set({ date }),
}));
