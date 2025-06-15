import * as fns from "date-fns";

export const formatDate = (
    date: Date | string,
    format: string = "yyyy-MM-dd",
): string => {
    const d = new Date(date);
    return fns.format(d, format);
};

export const formatTime = (
    time: string | null,
    format: string = "h:mm a",
): string => {
    if (!time) return "";

    // Handle various time formats
    let timeDate: Date;

    if (time.includes("T")) {
        // ISO datetime string
        timeDate = new Date(time);
    } else if (time.includes(":")) {
        // Time string like "09:38:00" or "21:30"
        const today = new Date();
        const [hours, minutes, seconds = "00"] =
            time.split(":");
        timeDate = new Date(
            today.getFullYear(),
            today.getMonth(),
            today.getDate(),
            parseInt(hours),
            parseInt(minutes),
            parseInt(seconds),
        );
    } else {
        // Fallback: treat as ISO string
        timeDate = new Date(time);
    }

    return fns.format(timeDate, format);
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

export const formatDuration = (
    startDate: string,
    endDate: string | null,
): string => {
    const start = new Date(startDate);
    const end = endDate
        ? new Date(endDate)
        : new Date();

    const diffInDays = fns.differenceInDays(
        end,
        start,
    );
    const diffInWeeks = fns.differenceInWeeks(
        end,
        start,
    );
    const diffInMonths = fns.differenceInMonths(
        end,
        start,
    );

    if (diffInDays === 0) {
        return "Same day";
    } else if (diffInDays === 1) {
        return "1 day";
    } else if (diffInDays < 7) {
        return `${diffInDays} days`;
    } else if (diffInWeeks === 1) {
        return "1 week";
    } else if (diffInWeeks < 4) {
        return `${diffInWeeks} weeks`;
    } else if (diffInMonths === 1) {
        return "1 month";
    } else if (diffInMonths < 12) {
        return `${diffInMonths} months`;
    } else {
        const years = Math.floor(
            diffInMonths / 12,
        );
        const remainingMonths = diffInMonths % 12;
        if (
            years === 1 &&
            remainingMonths === 0
        ) {
            return "1 year";
        } else if (remainingMonths === 0) {
            return `${years} years`;
        } else {
            return `${years} year${years > 1 ? "s" : ""} ${remainingMonths} month${remainingMonths > 1 ? "s" : ""}`;
        }
    }
};

export const getRelativeDate = (
    date: string,
): string => {
    const targetDate = new Date(date);
    const now = new Date();

    if (fns.isSameDay(targetDate, now)) {
        return "Today";
    } else if (
        fns.isSameDay(
            targetDate,
            fns.addDays(now, 1),
        )
    ) {
        return "Tomorrow";
    } else if (
        fns.isSameDay(
            targetDate,
            fns.subDays(now, 1),
        )
    ) {
        return "Yesterday";
    } else if (fns.isThisWeek(targetDate)) {
        return fns.format(targetDate, "EEEE"); // Day name
    } else if (fns.isThisYear(targetDate)) {
        return fns.format(targetDate, "MMM dd"); // Month day
    } else {
        return fns.format(
            targetDate,
            "MMM dd, yyyy",
        ); // Full date
    }
};
