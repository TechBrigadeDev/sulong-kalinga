import { create } from "zustand";
import { IUser } from "./user.schema";

interface State {
  user: IUser | null;
  setUser: (user: IUser | null) => void;
}

export const userStore = create<State>((set) => ({
  user: null,
  setUser: (user) => set({ user }),
}));
