import { formatDistanceToNow } from "date-fns";
import {
    AlertTriangle,
    Calendar,
    CheckCircle,
    Clock,
    FileText,
    Info,
    Lock,
    Settings,
    Shield,
    User,
    Users,
} from "lucide-react-native";

export const getNotificationIcon = (
    title: string,
) => {
    const lowerTitle = title.toLowerCase();

    // Map notification titles to appropriate icons
    if (
        lowerTitle.includes("system") ||
        lowerTitle.includes("update")
    ) {
        return Settings;
    }
    if (
        lowerTitle.includes("performance") ||
        lowerTitle.includes("report")
    ) {
        return FileText;
    }
    if (
        lowerTitle.includes("backup") ||
        lowerTitle.includes("data")
    ) {
        return CheckCircle;
    }
    if (
        lowerTitle.includes("registration") ||
        lowerTitle.includes("user")
    ) {
        return User;
    }
    if (
        lowerTitle.includes("security") ||
        lowerTitle.includes("alert")
    ) {
        return Shield;
    }
    if (
        lowerTitle.includes("appointment") ||
        lowerTitle.includes("schedule")
    ) {
        return Calendar;
    }
    if (lowerTitle.includes("password")) {
        return Lock;
    }
    if (
        lowerTitle.includes("staff") ||
        lowerTitle.includes("team")
    ) {
        return Users;
    }
    if (
        lowerTitle.includes("time") ||
        lowerTitle.includes("reminder")
    ) {
        return Clock;
    }
    if (
        lowerTitle.includes("warning") ||
        lowerTitle.includes("important")
    ) {
        return AlertTriangle;
    }

    // Default fallback
    return Info;
};

export const getNotificationIconColor = (
    title: string,
    isRead: boolean,
) => {
    const lowerTitle = title.toLowerCase();

    if (!isRead) {
        // Unread notifications get more vibrant colors
        if (
            lowerTitle.includes("system") ||
            lowerTitle.includes("update")
        ) {
            return "#3b82f6"; // Blue
        }
        if (
            lowerTitle.includes("security") ||
            lowerTitle.includes("alert")
        ) {
            return "#ef4444"; // Red
        }
        if (
            lowerTitle.includes("performance") ||
            lowerTitle.includes("report")
        ) {
            return "#8b5cf6"; // Purple
        }
        if (
            lowerTitle.includes("backup") ||
            lowerTitle.includes("data")
        ) {
            return "#10b981"; // Green
        }
        if (
            lowerTitle.includes("appointment") ||
            lowerTitle.includes("schedule")
        ) {
            return "#f59e0b"; // Amber
        }
        return "#6366f1"; // Default blue
    } else {
        // Read notifications get muted colors
        return "#9ca3af"; // Gray
    }
};

export const getRelativeTime = (
    dateString: string,
) => {
    try {
        const date = new Date(dateString);
        return formatDistanceToNow(date, {
            addSuffix: true,
        });
    } catch {
        return "Unknown time";
    }
};

export const getNotificationBorderColor = (
    title: string,
    isRead: boolean,
) => {
    if (isRead) return "transparent";

    const lowerTitle = title.toLowerCase();

    if (
        lowerTitle.includes("security") ||
        lowerTitle.includes("alert")
    ) {
        return "#ef4444"; // Red border for security alerts
    }
    if (
        lowerTitle.includes("system") ||
        lowerTitle.includes("update")
    ) {
        return "#3b82f6"; // Blue border for system updates
    }

    return "#6366f1"; // Default blue border for unread
};
