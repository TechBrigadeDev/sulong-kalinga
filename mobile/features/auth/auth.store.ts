import { create, StateCreator } from "zustand";
import {
    createJSONStorage,
    persist,
} from "zustand/middleware";

import { secureStorage } from "~/common/storage/secure.store";

interface State {
    token: string | null;
    setToken: (token: string | null) => void;
}

const store: StateCreator<State> = (set) => ({
    token: null,
    setToken: (token) => {
        set({ token });
    },
});

export const authStore = create<State>()(
    persist(store, {
        name: "auth-storage",
        storage: createJSONStorage(
            () => secureStorage,
        ),
    }),
);
