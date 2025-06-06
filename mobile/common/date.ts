import { format as fnsFormat } from "date-fns";

export const formatDate = (
    date: Date | string,
    format: string = "yyyy-MM-dd",
): string => {
    const d = new Date(date);
    return fnsFormat(d, format);
};
