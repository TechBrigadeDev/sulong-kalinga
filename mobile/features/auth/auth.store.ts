import { create, StateCreator } from "zustand";
import {
    createJSONStorage,
    persist,
} from "zustand/middleware";

import { secureStorage } from "~/common/storage/secure.store";

import { IRole, IUser } from "./auth.interface";

interface State {
    token: string | null;
    setToken: (token: string | null) => void;
    role: IRole | null;
    setRole: (role: IRole | null) => void;
    user: IUser | null;
    setUser: (user: IUser | null) => void;
    clear: () => void;
    notificationToken: string | null;
    setNotificationToken: (
        token: string | null,
    ) => void;
}

const store: StateCreator<State> = (set) => ({
    token: null,
    setToken: (token) => {
        set({ token });
    },
    role: null,
    setRole: (role) => {
        set({ role });
    },
    user: null,
    setUser: (user) =>
        set({
            user,
        }),
    notificationToken: null,
    setNotificationToken: (token) => {
        set({ notificationToken: token });
    },
    clear: () => {
        set({
            token: null,
            role: null,
            user: null,
        });
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
