import { create } from "zustand";

interface State {
    isOpen: boolean;
    setIsOpen: (isOpen: boolean) => void;
}

export const updateEmailStore = create<State>((set) => ({
    isOpen: false,
    setIsOpen: (isOpen) => set({ isOpen }),
}));