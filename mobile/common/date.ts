import * as fns from "date-fns";

export const formatDate = (
    date: Date | string,
    format: string = "yyyy-MM-dd",
): string => {
    const d = new Date(date);
    return fns.format(d, format);
};

export const startOfWeekDate = (
    date: Date | string,
): Date => {
    const d = new Date(date);
    return fns.startOfWeek(d);
};

export const endOfWeekDate = (
    date: Date | string,
): Date => {
    const d = new Date(date);
    return fns.endOfWeek(d);
};

export const isSameDay = (
    dateLeft: Date | string,
    dateRight: Date | string,
): boolean => {
    const d1 = new Date(dateLeft);
    const d2 = new Date(dateRight);
    return fns.isSameDay(d1, d2);
};
