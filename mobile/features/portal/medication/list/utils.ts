import { formatTime } from "common/date";

import { MedicationSchedule } from "./types";

export const getStatusColor = (
    status: string,
) => {
    switch (status) {
        case "completed":
            return "#22c55e";
        case "discontinued":
            return "#ef4444";
        case "paused":
            return "#f59e0b";
        case "active":
        default:
            return "#3b82f6";
    }
};

export const getScheduleTimes = (
    item: MedicationSchedule,
) => {
    const times = [];
    if (item.morning_time)
        times.push(
            `Morning: ${formatTime(item.morning_time)}`,
        );
    if (item.noon_time)
        times.push(
            `Noon: ${formatTime(item.noon_time)}`,
        );
    if (item.evening_time)
        times.push(
            `Evening: ${formatTime(item.evening_time)}`,
        );
    if (item.night_time)
        times.push(
            `Night: ${formatTime(item.night_time)}`,
        );
    if (item.as_needed) times.push("As needed");

    return times.length > 0
        ? times.join(" • ")
        : "No specific times";
};
