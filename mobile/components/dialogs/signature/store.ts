import { create } from "zustand";

interface SignatureStore {
    isOpen: boolean;
    signature: string | null;
    title: string;
    onSave?: (signature: string) => void;
    setIsOpen: (isOpen: boolean) => void;
    setSignature: (
        signature: string | null,
    ) => void;
    setTitle: (title: string) => void;
    setOnSave: (
        callback: (signature: string) => void,
    ) => void;
    reset: () => void;
}

export const useSignatureStore =
    create<SignatureStore>((set) => ({
        isOpen: false,
        signature: null,
        title: "",
        onSave: undefined,
        setIsOpen: (isOpen) => set({ isOpen }),
        setSignature: (signature) =>
            set({ signature }),
        setTitle: (title) => set({ title }),
        setOnSave: (callback) =>
            set({ onSave: callback }),
        reset: () =>
            set({
                isOpen: false,
                signature: null,
                title: "",
                onSave: undefined,
            }),
    }));
