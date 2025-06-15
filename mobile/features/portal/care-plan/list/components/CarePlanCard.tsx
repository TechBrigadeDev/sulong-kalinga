import { formatDate } from "common/date";
import { portalCarePlanListSchema } from "features/portal/care-plan/schema";
import {
    EyeIcon,
    FileText,
    User,
} from "lucide-react-native";
import { StyleSheet } from "react-native";
import {
    Button,
    Card,
    Text,
    XStack,
    YStack,
} from "tamagui";
import { z } from "zod";

type ICarePlan = z.infer<
    typeof portalCarePlanListSchema
>;

interface Props {
    carePlan: ICarePlan;
    onView?: (id: number) => void;
    onAcknowledge?: (id: number) => void;
}

const CarePlanCard = ({
    carePlan,
    onView,
    onAcknowledge,
}: Props) => {
    const getStatusColor = (status: string) => {
        switch (status.toLowerCase()) {
            case "pending review":
                return "#fbbf24"; // amber
            case "acknowledged":
                return "#10b981"; // emerald
            case "completed":
                return "#6b7280"; // gray
            default:
                return "#6b7280";
        }
    };

    const getStatusTextColor = (
        status: string,
    ) => {
        switch (status.toLowerCase()) {
            case "pending review":
                return "#92400e"; // amber-800
            case "acknowledged":
                return "#065f46"; // emerald-800
            case "completed":
                return "#374151"; // gray-700
            default:
                return "#374151";
        }
    };

    const canAcknowledge =
        carePlan.status === "Pending Review" &&
        !carePlan.acknowledged;

    return (
        <Card
            elevate
            marginBottom="$3"
            style={styles.card}
            pressStyle={{
                scale: 0.98,
                opacity: 0.9,
            }}
        >
            <Card.Header p="$4">
                <YStack gap="$3">
                    {/* Header Row with Status */}
                    <XStack
                        justify="space-between"
                        items="center"
                    >
                        <XStack
                            items="center"
                            gap="$2"
                            flex={1}
                        >
                            <FileText
                                size={20}
                                color="#1f2937"
                            />
                            <Text
                                fontSize="$5"
                                fontWeight="600"
                                color="#1f2937"
                                flex={1}
                                numberOfLines={1}
                            >
                                Care Plan #
                                {carePlan.id}
                            </Text>
                        </XStack>
                        <XStack
                            bg={getStatusColor(
                                carePlan.status,
                            )}
                            px="$2"
                            py="$1"
                            rounded="$3"
                            items="center"
                        >
                            <Text
                                fontSize="$2"
                                fontWeight="600"
                                color={getStatusTextColor(
                                    carePlan.status,
                                )}
                            >
                                {carePlan.status}
                            </Text>
                        </XStack>
                    </XStack>

                    {/* Author Information */}
                    <XStack
                        items="center"
                        gap="$2"
                    >
                        <User
                            size={16}
                            color="#6b7280"
                        />
                        <Text
                            fontSize="$4"
                            color="#6b7280"
                        >
                            Author:
                        </Text>
                        <Text
                            fontSize="$4"
                            fontWeight="500"
                            color="#374151"
                        >
                            {carePlan.author_name}
                        </Text>
                    </XStack>

                    {/* Date Information */}
                    <XStack
                        items="center"
                        gap="$2"
                    >
                        <Text
                            fontSize="$4"
                            color="#6b7280"
                        >
                            Date Created:
                        </Text>
                        <Text
                            fontSize="$4"
                            fontWeight="500"
                            color="#374151"
                        >
                            {formatDate(
                                new Date(
                                    carePlan.date,
                                ),
                            )}
                        </Text>
                    </XStack>

                    {/* Acknowledged Information */}
                    {carePlan.acknowledged && (
                        <XStack
                            items="center"
                            gap="$2"
                        >
                            <Text
                                fontSize="$4"
                                color="#6b7280"
                            >
                                Acknowledged:
                            </Text>
                            <Text
                                fontSize="$4"
                                fontWeight="500"
                                color="#059669"
                            >
                                {formatDate(
                                    new Date(
                                        carePlan.acknowledged,
                                    ),
                                )}
                            </Text>
                        </XStack>
                    )}

                    {/* Action Buttons */}
                    <XStack
                        gap="$2"
                        marginBlockStart="$2"
                    >
                        <Button
                            size="$3"
                            variant="outlined"
                            flex={1}
                            onPress={() =>
                                onView?.(
                                    carePlan.id,
                                )
                            }
                            icon={
                                <EyeIcon
                                    size={16}
                                />
                            }
                            borderColor="#e5e7eb"
                            bg="#f9fafb"
                            color="#374151"
                            hoverStyle={{
                                bg: "#f3f4f6",
                            }}
                        >
                            View
                        </Button>
                        {canAcknowledge && (
                            <Button
                                size="$3"
                                theme="green"
                                flex={1}
                                onPress={() =>
                                    onAcknowledge?.(
                                        carePlan.id,
                                    )
                                }
                                bg="#10b981"
                                color="white"
                                hoverStyle={{
                                    bg: "#059669",
                                }}
                            >
                                Acknowledge
                            </Button>
                        )}
                    </XStack>
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
        backgroundColor: "#ffffff",
        shadowColor: "#000",
        shadowOffset: {
            width: 0,
            height: 1,
        },
        shadowOpacity: 0.1,
        shadowRadius: 3,
        elevation: 2,
    },
});

export default CarePlanCard;
