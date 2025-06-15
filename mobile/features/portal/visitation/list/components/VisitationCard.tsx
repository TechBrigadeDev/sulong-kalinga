import { formatDistance } from "date-fns";
import { IVisitation } from "features/portal/visitation/type";
import {
    Calendar,
    Clock,
    MapPin,
} from "lucide-react-native";
import { useMemo } from "react";
import {
    Card,
    GetThemeValueForKey,
    Text,
    XStack,
    YStack,
} from "tamagui";

interface Props {
    visitation: IVisitation;
}

const VisitationCard = ({ visitation }: Props) => {
    const occurrenceDate = new Date(visitation.occurrence_date);
    const now = new Date();

    const { backgroundColor, textColor } = useMemo(() => {
        const getStatusColors = (status: IVisitation["status"]): {
            backgroundColor: GetThemeValueForKey<"backgroundColor">;
            textColor: GetThemeValueForKey<"color">;
        } => {
            switch (status) {
                case "completed":
                    return {
                        backgroundColor: "$green2",
                        textColor: "$green11",
                    };
                case "canceled":
                    return {
                        backgroundColor: "$red2",
                        textColor: "$red11",
                    };
                case "scheduled":
                    return {
                        backgroundColor: "$blue2",
                        textColor: "$blue11",
                    };
                default:
                    return {
                        backgroundColor: "$gray2",
                        textColor: "$gray11",
                    };
            }
        };

        return getStatusColors(visitation.status);
    }, [visitation.status]);

    const formatTime = (timeString: string | null) => {
        if (!timeString) return "Flexible Time";
        try {
            return new Date(timeString).toLocaleTimeString([], {
                hour: "2-digit",
                minute: "2-digit",
            });
        } catch {
            return "Invalid Time";
        }
    };

    const getTimeRange = () => {
        if (!visitation.start_time || !visitation.end_time) {
            return "Flexible Time";
        }
        return `${formatTime(visitation.start_time)} - ${formatTime(visitation.end_time)}`;
    };

    const getRelativeTime = () => {
        try {
            if (occurrenceDate < now) {
                return `${formatDistance(occurrenceDate, now)} ago`;
            } else {
                return `in ${formatDistance(now, occurrenceDate)}`;
            }
        } catch {
            return "Invalid date";
        }
    };

    const getStatusLabel = (status: IVisitation["status"]) => {
        switch (status) {
            case "scheduled":
                return "Scheduled";
            case "completed":
                return "Completed";
            case "canceled":
                return "Canceled";
            default:
                return "Unknown";
        }
    };

    return (
        <Card
            marginBottom="$3"
            backgroundColor={backgroundColor}
            borderRadius="$4"
            borderWidth={1}
            borderColor="$borderColor"
            elevation="$1"
            pressStyle={{
                scale: 0.98,
                opacity: 0.8,
            }}
        >
            <Card.Header p="$4">
                <YStack gap="$3">
                    {/* Status Badge */}
                    <XStack justifyContent="space-between" alignItems="center">
                        <Text
                            fontSize="$3"
                            fontWeight="600"
                            color={textColor}
                            backgroundColor="$background"
                            paddingHorizontal="$2"
                            paddingVertical="$1"
                            borderRadius="$2"
                        >
                            {getStatusLabel(visitation.status)}
                        </Text>
                        <Text
                            fontSize="$2"
                            color="$gray10"
                            fontWeight="500"
                        >
                            {getRelativeTime()}
                        </Text>
                    </XStack>

                    {/* Date Information */}
                    <XStack alignItems="center" gap="$2">
                        <Calendar size={16} color="$gray10" />
                        <Text
                            fontSize="$4"
                            fontWeight="600"
                            color="$color"
                        >
                            {occurrenceDate.toLocaleDateString("en-US", {
                                weekday: "long",
                                year: "numeric",
                                month: "long",
                                day: "numeric",
                            })}
                        </Text>
                    </XStack>

                    {/* Time Information */}
                    <XStack alignItems="center" gap="$2">
                        <Clock size={16} color="$gray10" />
                        <Text
                            fontSize="$3"
                            color="$gray11"
                        >
                            {getTimeRange()}
                        </Text>
                    </XStack>

                    {/* Notes if available */}
                    {visitation.notes && (
                        <YStack gap="$1">
                            <Text
                                fontSize="$3"
                                fontWeight="600"
                                color="$gray11"
                            >
                                Notes:
                            </Text>
                            <Text
                                fontSize="$3"
                                color="$gray10"
                                lineHeight="$1"
                            >
                                {visitation.notes}
                            </Text>
                        </YStack>
                    )}

                    {/* Modified Indicator */}
                    {visitation.is_modified && (
                        <XStack alignItems="center" gap="$1">
                            <MapPin size={12} color="$orange10" />
                            <Text
                                fontSize="$2"
                                color="$orange10"
                                fontStyle="italic"
                            >
                                Schedule modified
                            </Text>
                        </XStack>
                    )}
                </YStack>
            </Card.Header>
        </Card>
    );
};

export default VisitationCard;
