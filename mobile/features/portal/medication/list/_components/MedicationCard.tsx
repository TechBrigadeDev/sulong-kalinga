import {
    formatDate,
    getRelativeDate,
} from "common/date";
import { MedicationSchedule } from "features/portal/medication/list/types";
import {
    getScheduleTimes,
    getStatusColor,
} from "features/portal/medication/list/utils";
import {
    Calendar,
    Clock,
    Eye,
    Pill,
} from "lucide-react-native";
import {
    Button,
    Card,
    Text,
    XStack,
    YStack,
} from "tamagui";

interface MedicationCardProps {
    item: MedicationSchedule;
    onViewDetails: (medicationId: number) => void;
}

export const MedicationCard = ({
    item,
    onViewDetails,
}: MedicationCardProps) => {
    // Defensive check for undefined/null item
    if (!item) {
        return null;
    }

    const startDate = formatDate(
        item.start_date,
        "MMM dd, yyyy",
    );
    const endDate = item.end_date
        ? formatDate(
              item.end_date,
              "MMM dd, yyyy",
          )
        : null;

    const relativeStartDate = getRelativeDate(
        item.start_date,
    );
    const relativeEndDate = item.end_date
        ? getRelativeDate(item.end_date)
        : null;

    return (
        <Card
            elevate
            m="$2"
            p="$4"
            style={{
                backgroundColor: "white",
                borderColor: "#e5e7eb",
                borderRadius: 16,
                shadowColor: "#000",
                shadowOffset: {
                    width: 0,
                    height: 2,
                },
                shadowOpacity: 0.1,
                shadowRadius: 4,
                elevation: 3,
            }}
            borderWidth={1}
            pressStyle={{
                scale: 0.98,
            }}
            animation="quick"
        >
            <YStack gap="$3">
                {/* Header with medication name and action button */}
                <XStack
                    style={{
                        justifyContent:
                            "space-between",
                        alignItems: "flex-start",
                    }}
                >
                    <YStack flex={1} gap="$1">
                        <XStack
                            style={{
                                alignItems:
                                    "center",
                            }}
                            gap="$2"
                        >
                            <Pill
                                size={20}
                                color="#3b82f6"
                            />
                            <Text
                                fontSize="$6"
                                fontWeight="bold"
                                color="#111827"
                                numberOfLines={2}
                                style={{
                                    flexShrink: 1,
                                }}
                            >
                                {
                                    item.medication_name
                                }
                            </Text>
                        </XStack>
                        <Text
                            fontSize="$4"
                            fontWeight="500"
                            style={{
                                color: "#6b7280",
                            }}
                        >
                            {item.medication_type}{" "}
                            • {item.dosage}
                        </Text>
                    </YStack>

                    <Button
                        size="$3"
                        variant="outlined"
                        borderColor="#3b82f6"
                        ml="$2"
                        onPress={() =>
                            onViewDetails(
                                item.medication_schedule_id,
                            )
                        }
                    >
                        <Eye
                            size={16}
                            color="#3b82f6"
                        />
                    </Button>
                </XStack>

                {/* Schedule times */}
                <YStack gap="$2">
                    <XStack
                        style={{
                            alignItems: "center",
                        }}
                        gap="$2"
                    >
                        <Clock
                            size={16}
                            color="#6b7280"
                        />
                        <Text
                            fontSize="$3"
                            fontWeight="600"
                            style={{
                                color: "#6b7280",
                            }}
                        >
                            Schedule:
                        </Text>
                    </XStack>
                    <Text
                        fontSize="$3"
                        lineHeight="$1"
                        style={{
                            color: "#374151",
                        }}
                    >
                        {getScheduleTimes(item)}
                    </Text>
                </YStack>

                {/* Date range and status */}
                <XStack
                    style={{
                        justifyContent:
                            "space-between",
                        alignItems: "center",
                        flexWrap: "wrap",
                    }}
                    gap="$2"
                >
                    <XStack
                        style={{
                            alignItems: "center",
                            flex: 1,
                        }}
                        gap="$2"
                    >
                        <Calendar
                            size={16}
                            color="#6b7280"
                        />
                        <YStack flex={1}>
                            <Text
                                fontSize="$3"
                                style={{
                                    color: "#6b7280",
                                    flexShrink: 1,
                                }}
                            >
                                {startDate}
                                {endDate
                                    ? ` - ${endDate}`
                                    : " (Ongoing)"}
                            </Text>
                            <Text
                                fontSize="$2"
                                style={{
                                    color: "#3b82f6",
                                }}
                            >
                                {
                                    relativeStartDate
                                }
                                {relativeEndDate
                                    ? ` - ${relativeEndDate}`
                                    : " (ongoing)"}
                            </Text>
                        </YStack>
                    </XStack>

                    <XStack
                        style={{
                            backgroundColor:
                                getStatusColor(
                                    item.status,
                                ),
                            paddingHorizontal: 8,
                            paddingVertical: 4,
                            borderRadius: 4,
                        }}
                    >
                        <Text
                            fontSize="$2"
                            color="white"
                            fontWeight="600"
                            textTransform="capitalize"
                        >
                            {item.status}
                        </Text>
                    </XStack>
                </XStack>

                {/* Special instructions if any */}
                {item.special_instructions && (
                    <YStack
                        style={{
                            padding: 12,
                            backgroundColor:
                                "#fef3c7",
                            borderRadius: 8,
                            borderLeftWidth: 3,
                            borderLeftColor:
                                "#f59e0b",
                        }}
                    >
                        <Text
                            fontSize="$2"
                            fontWeight="600"
                            style={{
                                color: "#92400e",
                                marginBottom: 4,
                            }}
                        >
                            Special Instructions:
                        </Text>
                        <Text
                            fontSize="$3"
                            lineHeight="$1"
                            style={{
                                color: "#78350f",
                            }}
                        >
                            {
                                item.special_instructions
                            }
                        </Text>
                    </YStack>
                )}
            </YStack>
        </Card>
    );
};
