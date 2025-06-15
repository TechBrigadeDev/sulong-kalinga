import { format } from "date-fns";
import { StyleSheet } from "react-native";
import {
    Card,
    Paragraph,
    Text,
    XStack,
    YStack,
} from "tamagui";

import Badge from "./Badge";
import type { EmergencyRequestHistory } from "./types";

interface RequestCardProps {
    request: EmergencyRequestHistory;
}

const RequestCard = ({
    request,
}: RequestCardProps) => {
    const getStatusColor = (status: string) => {
        switch (status.toLowerCase()) {
            case "resolved":
                return "#22c55e";
            case "completed":
                return "#22c55e";
            case "cancelled":
                return "#ef4444";
            default:
                return "#6b7280";
        }
    };

    const getTypeColor = (type: string) => {
        switch (type.toLowerCase()) {
            case "medication issue":
                return "#ef4444";
            case "medical emergency":
                return "#dc2626";
            case "fall":
                return "#f97316";
            case "general":
                return "#3b82f6";
            default:
                return "#6b7280";
        }
    };

    const formatDateShort = (
        dateString: string,
    ) => {
        try {
            return format(
                new Date(dateString),
                "MMM d, yyyy",
            );
        } catch {
            return dateString;
        }
    };

    return (
        <Card
            style={styles.requestCard}
            borderColor="#E9ECEF"
            borderWidth={1}
            backgroundColor="#FFFFFF"
        >
            {/* Header with Type and Status */}
            <XStack style={styles.requestHeader}>
                <Badge
                    backgroundColor={getTypeColor(
                        request.type,
                    )}
                >
                    {request.type}
                </Badge>
                <Badge
                    backgroundColor={getStatusColor(
                        request.status,
                    )}
                >
                    {request.status.toUpperCase()}
                </Badge>
            </XStack>

            {/* Description */}
            <Paragraph
                style={styles.description}
                numberOfLines={2}
            >
                {request.description}
            </Paragraph>

            {/* Date Information Row */}
            <XStack style={styles.detailsRow}>
                <YStack flex={1}>
                    <Text style={styles.label}>
                        DATE SUBMITTED
                    </Text>
                    <Text style={styles.value}>
                        {formatDateShort(
                            request.date_submitted,
                        )}
                    </Text>
                </YStack>
                {request.date_resolved && (
                    <YStack
                        flex={1}
                        style={{
                            alignItems: "center",
                        }}
                    >
                        <Text
                            style={styles.label}
                        >
                            DATE RESOLVED
                        </Text>
                        <Text
                            style={styles.value}
                        >
                            {formatDateShort(
                                request.date_resolved,
                            )}
                        </Text>
                    </YStack>
                )}
                <YStack
                    flex={1}
                    style={{
                        alignItems: "flex-end",
                    }}
                >
                    <Text style={styles.label}>
                        HANDLED BY
                    </Text>
                    <Text style={styles.value}>
                        {request.handled_by ||
                            request.assigned_to ||
                            "Not assigned"}
                    </Text>
                </YStack>
            </XStack>

            {/* Resolution Notes if available */}
            {request.resolution_notes && (
                <YStack
                    style={
                        styles.resolutionSection
                    }
                >
                    <Text style={styles.label}>
                        RESOLUTION NOTES
                    </Text>
                    <Text
                        style={
                            styles.resolutionNotes
                        }
                        numberOfLines={3}
                    >
                        {request.resolution_notes}
                    </Text>
                </YStack>
            )}
        </Card>
    );
};

const styles = StyleSheet.create({
    requestCard: {
        padding: 12,
        marginBottom: 8,
        borderRadius: 8,
        opacity: 0.9,
    },
    requestHeader: {
        justifyContent: "space-between",
        alignItems: "center",
        marginBottom: 8,
    },
    description: {
        marginBottom: 8,
        fontSize: 14,
        color: "#374151",
    },
    detailsRow: {
        justifyContent: "space-between",
        alignItems: "flex-start",
        marginBottom: 8,
    },
    label: {
        fontSize: 10,
        opacity: 0.6,
        fontWeight: "500",
        marginBottom: 2,
    },
    value: {
        fontSize: 12,
        color: "#374151",
    },
    resolutionSection: {
        marginTop: 8,
        paddingTop: 8,
        borderTopWidth: 1,
        borderTopColor: "#E5E7EB",
    },
    resolutionNotes: {
        fontSize: 12,
        color: "#6B7280",
        fontStyle: "italic",
        marginTop: 2,
    },
});

export default RequestCard;
