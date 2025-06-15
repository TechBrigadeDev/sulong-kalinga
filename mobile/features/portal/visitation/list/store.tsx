import {
    endOfWeekDate,
    startOfWeekDate,
} from "common/date";
import { create } from "zustand";

interface State {
    currentDate: Date;
    setCurrentDate: (date: Date) => void;

    startOfWeek: Date;
    endOfWeek: Date;
}

export const portalVisitationListStore =
    create<State>((set) => ({
        currentDate: new Date(),
        setCurrentDate: (date) => {
            set({
                currentDate: date,
                startOfWeek:
                    startOfWeekDate(date),
                endOfWeek: endOfWeekDate(date),
            });
        },
        startOfWeek: startOfWeekDate(new Date()),
        endOfWeek: endOfWeekDate(new Date()),
    }));
