import { format } from "date-fns";
import { useEmergencyCancelRequest } from "features/portal/emergency-service/emergency/hook";
import { useEmergencyServiceStore } from "features/portal/emergency-service/store";
import {
    ICurrentEmergencyServiceForm,
    IEmergencyServiceRequest,
} from "features/portal/emergency-service/type";
import { PenBox } from "lucide-react-native";
import { StyleSheet } from "react-native";
import { showToastable } from "react-native-toastable";
import {
    Button,
    Card,
    Paragraph,
    Spinner,
    Text,
    XStack,
    YStack,
} from "tamagui";

import Badge from "./Badge";

interface RequestCardProps {
    request: IEmergencyServiceRequest;
}

const RequestCard = ({
    request,
}: RequestCardProps) => {
    const getStatusColor = (status: string) => {
        switch (status.toLowerCase()) {
            case "new":
                return "#f97316";
            case "in_progress":
                return "#3b82f6";
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

    const formatDate = (dateString: string) => {
        try {
            return format(
                new Date(dateString),
                "MMM d, yyyy h:mm a",
            );
        } catch {
            return dateString;
        }
    };

    const store = useEmergencyServiceStore();

    const handleEdit = () => {
        if (!request) {
            return;
        }

        store.setState((state) => ({
            ...state,
            currentEmergencyServiceForm:
                request.type as ICurrentEmergencyServiceForm,
            request,
        }));
    };

    const {
        mutate: cancelRequest,
        isPending: isCancelling,
    } = useEmergencyCancelRequest();
    const handleCancel = async () => {
        try {
            cancelRequest(request.id.toString());
            showToastable({
                title: "Request Cancelled",
                message:
                    "Your emergency request has been cancelled successfully.",
            });
        } catch (error) {
            console.error(
                `Error cancelling request ${request.id}:`,
                error,
            );
        }
    };

    // const {
    //     mutate: deleteRequest,
    //     isPending: isDeleting,
    // } = useEmergencyDeleteRequest();

    // const handleDelete = async () => {
    //     try {
    //         deleteRequest(request.id.toString());
    //         showToastable({
    //             title: "Request Deleted",
    //             message:
    //                 "Your emergency request has been deleted successfully.",
    //         });
    //     } catch (error) {
    //         console.error(
    //             `Error deleting request ${request.id}:`,
    //             error,
    //         );
    //     }
    // };

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
                    {request.status
                        .replace("_", " ")
                        .toUpperCase()}
                </Badge>
            </XStack>

            {/* Description */}
            <Paragraph
                style={styles.description}
                numberOfLines={2}
            >
                {request.description}
            </Paragraph>

            {/* Details Row */}
            <XStack style={styles.detailsRow}>
                <YStack flex={1}>
                    <Text style={styles.label}>
                        DATE SUBMITTED
                    </Text>
                    <Text style={styles.value}>
                        {formatDate(
                            request.date_submitted,
                        )}
                    </Text>
                </YStack>
                <YStack
                    flex={1}
                    style={{
                        alignItems: "flex-end",
                    }}
                >
                    <Text style={styles.label}>
                        ASSIGNED TO
                    </Text>
                    <Text style={styles.value}>
                        {request.assigned_to ||
                            "Not assigned"}
                    </Text>
                </YStack>
            </XStack>

            {/* Action Buttons */}
            <XStack style={styles.actionButtons}>
                <Button
                    size="$2"
                    theme="green"
                    flex={1}
                    onPress={handleEdit}
                >
                    <PenBox
                        color="black"
                        size={16}
                        style={{ marginRight: 4 }}
                    />
                    <Text
                        color="$color"
                        fontWeight="bold"
                    >
                        Edit
                    </Text>
                </Button>
                <Button
                    size="$2"
                    variant="outlined"
                    theme="yellow"
                    flex={1}
                    onPress={handleCancel}
                >
                    {isCancelling ? (
                        <Spinner
                            size="small"
                            mr="$2"
                        />
                    ) : (
                        "Cancel"
                    )}
                </Button>
                {/* <Button
                    size="$2"
                    variant="outlined"
                    theme="red"
                    flex={1}
                    onPress={handleDelete}
                    disabled={isDeleting}
                >
                    {isDeleting ? (
                        <Spinner
                            size="small"
                            mr="$2"
                        />
                    ) : (
                        "Delete"
                    )}
                </Button> */}
            </XStack>
        </Card>
    );
};

const styles = StyleSheet.create({
    requestCard: {
        padding: 12,
        marginBottom: 8,
        borderRadius: 8,
    },
    requestHeader: {
        justifyContent: "space-between",
        alignItems: "center",
        marginBottom: 8,
    },
    description: {
        marginBottom: 8,
        fontSize: 14,
    },
    detailsRow: {
        justifyContent: "space-between",
        alignItems: "center",
        marginBottom: 8,
    },
    label: {
        fontSize: 10,
        opacity: 0.6,
        fontWeight: "500",
    },
    value: {
        fontSize: 12,
    },
    actionButtons: {
        gap: 8,
        marginTop: 8,
    },
});

export default RequestCard;
