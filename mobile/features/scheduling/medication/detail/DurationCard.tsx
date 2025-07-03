import {
    formatDate,
    formatDuration,
    getRelativeDate,
} from "common/date";
import { Calendar } from "lucide-react-native";
import {
    Card,
    H4,
    Text,
    XStack,
    YStack,
} from "tamagui";

interface DurationCardProps {
    start_date?: string;
    end_date?: string;
}

const DurationCard = (
    props: DurationCardProps,
) => {
    const { start_date, end_date } = props || {};

    if (!start_date) return null;

    const startDate = formatDate(
        start_date,
        "MMMM dd, yyyy",
    );
    const endDate = end_date
        ? formatDate(end_date, "MMMM dd, yyyy")
        : null;

    const duration = formatDuration(
        start_date,
        end_date || null,
    );

    const relativeStartDate =
        getRelativeDate(start_date);
    const relativeEndDate = end_date
        ? getRelativeDate(end_date)
        : null;

    return (
        <Card
            elevate
            mb="$4"
            p="$4"
            style={{ borderRadius: 16 }}
        >
            <YStack gap="$3">
                <XStack
                    style={{
                        alignItems: "center",
                    }}
                    gap="$2"
                >
                    <Calendar
                        size={24}
                        color="#3b82f6"
                    />
                    <H4 color="#111827">
                        Duration
                    </H4>
                </XStack>

                <YStack gap="$3">
                    <XStack
                        style={{
                            justifyContent:
                                "space-between",
                        }}
                    >
                        <Text
                            fontSize="$4"
                            style={{
                                color: "#6b7280",
                            }}
                        >
                            Start Date:
                        </Text>
                        <YStack
                            style={{
                                alignItems:
                                    "flex-end",
                            }}
                        >
                            <Text
                                fontSize="$4"
                                fontWeight="500"
                            >
                                {startDate}
                            </Text>
                            <Text
                                fontSize="$3"
                                style={{
                                    color: "#3b82f6",
                                }}
                            >
                                {
                                    relativeStartDate
                                }
                            </Text>
                        </YStack>
                    </XStack>

                    <XStack
                        style={{
                            justifyContent:
                                "space-between",
                        }}
                    >
                        <Text
                            fontSize="$4"
                            style={{
                                color: "#6b7280",
                            }}
                        >
                            End Date:
                        </Text>
                        <YStack
                            style={{
                                alignItems:
                                    "flex-end",
                            }}
                        >
                            <Text
                                fontSize="$4"
                                fontWeight="500"
                            >
                                {endDate ||
                                    "Ongoing"}
                            </Text>
                            {relativeEndDate && (
                                <Text
                                    fontSize="$3"
                                    style={{
                                        color: "#3b82f6",
                                    }}
                                >
                                    {
                                        relativeEndDate
                                    }
                                </Text>
                            )}
                        </YStack>
                    </XStack>

                    <XStack
                        style={{
                            backgroundColor:
                                "#f0f9ff",
                            padding: 12,
                            borderRadius: 8,
                            justifyContent:
                                "space-between",
                            alignItems: "center",
                        }}
                    >
                        <Text
                            fontSize="$4"
                            style={{
                                color: "#0369a1",
                            }}
                            fontWeight="500"
                        >
                            Duration:
                        </Text>
                        <Text
                            fontSize="$4"
                            style={{
                                color: "#0369a1",
                            }}
                            fontWeight="600"
                        >
                            {end_date
                                ? duration
                                : `${duration} (ongoing)`}
                        </Text>
                    </XStack>
                </YStack>
            </YStack>
        </Card>
    );
};

export default DurationCard;
