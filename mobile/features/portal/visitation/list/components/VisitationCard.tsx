import { formatTime } from "common/date";
import { formatDistance } from "date-fns";
import { IVisitation } from "features/portal/visitation/type";
import {
    Calendar,
    Clock,
    MapPin,
} from "lucide-react-native";
import { useMemo } from "react";
import { StyleSheet, View } from "react-native";
import { Card, Text, YStack } from "tamagui";

interface Props {
    visitation: IVisitation;
}

const VisitationCard = ({
    visitation,
}: Props) => {
    const occurrenceDate = new Date(
        visitation.occurrence_date,
    );
    const now = new Date();

    const { backgroundColor, textColor } =
        useMemo(() => {
            switch (visitation.status) {
                case "completed":
                    return {
                        backgroundColor:
                            "#dcfce7",
                        textColor: "#166534",
                    };
                case "canceled":
                    return {
                        backgroundColor:
                            "#fee2e2",
                        textColor: "#991b1b",
                    };
                case "scheduled":
                    return {
                        backgroundColor:
                            "#dbeafe",
                        textColor: "#1e40af",
                    };
                default:
                    return {
                        backgroundColor:
                            "#f3f4f6",
                        textColor: "#374151",
                    };
            }
        }, [visitation.status]);

    const getTimeRange = () => {
        if (
            !visitation.start_time ||
            !visitation.end_time
        ) {
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

    const getStatusLabel = (
        status: IVisitation["status"],
    ) => {
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
            style={[
                styles.card,
                { backgroundColor },
            ]}
            elevation="$1"
            pressStyle={{
                scale: 0.98,
                opacity: 0.8,
            }}
        >
            <Card.Header p="$4">
                <YStack gap="$3">
                    {/* Status Badge */}
                    <View
                        style={
                            styles.statusHeader
                        }
                    >
                        <View
                            style={
                                styles.statusBadge
                            }
                        >
                            <Text
                                fontSize="$3"
                                fontWeight="600"
                                style={{
                                    color: textColor,
                                }}
                            >
                                {getStatusLabel(
                                    visitation.status,
                                )}
                            </Text>
                        </View>
                        <Text
                            fontSize="$2"
                            color="#6b7280"
                            fontWeight="500"
                        >
                            {getRelativeTime()}
                        </Text>
                    </View>

                    {/* Date Information */}
                    <View style={styles.infoRow}>
                        <Calendar
                            size={16}
                            color="#6b7280"
                        />
                        <Text
                            fontSize="$4"
                            fontWeight="600"
                            color="#111827"
                        >
                            {occurrenceDate.toLocaleDateString(
                                "en-US",
                                {
                                    weekday:
                                        "long",
                                    year: "numeric",
                                    month: "long",
                                    day: "numeric",
                                },
                            )}
                        </Text>
                    </View>

                    {/* Time Information */}
                    <View style={styles.infoRow}>
                        <Clock
                            size={16}
                            color="#6b7280"
                        />
                        <Text
                            fontSize="$3"
                            color="#4b5563"
                        >
                            {getTimeRange()}
                        </Text>
                    </View>

                    {/* Notes if available */}
                    {visitation.notes && (
                        <YStack gap="$1">
                            <Text
                                fontSize="$3"
                                fontWeight="600"
                                color="#374151"
                            >
                                Notes:
                            </Text>
                            <Text
                                fontSize="$3"
                                color="#6b7280"
                                lineHeight="$1"
                            >
                                {visitation.notes}
                            </Text>
                        </YStack>
                    )}

                    {/* Modified Indicator */}
                    {visitation.is_modified && (
                        <View
                            style={
                                styles.modifiedRow
                            }
                        >
                            <MapPin
                                size={12}
                                color="#f59e0b"
                            />
                            <Text
                                fontSize="$2"
                                color="#f59e0b"
                                style={{
                                    fontStyle:
                                        "italic",
                                }}
                            >
                                Schedule modified
                            </Text>
                        </View>
                    )}
                </YStack>
            </Card.Header>
        </Card>
    );
};

const styles = StyleSheet.create({
    card: {
        borderRadius: 12,
        borderWidth: 1,
        borderColor: "#e5e7eb",
    },
    statusHeader: {
        flexDirection: "row",
        justifyContent: "space-between",
        alignItems: "center",
    },
    statusBadge: {
        backgroundColor: "#ffffff",
        paddingHorizontal: 8,
        paddingVertical: 4,
        borderRadius: 6,
    },
    infoRow: {
        flexDirection: "row",
        alignItems: "center",
        gap: 8,
    },
    modifiedRow: {
        flexDirection: "row",
        alignItems: "center",
        gap: 4,
    },
});

export default VisitationCard;
