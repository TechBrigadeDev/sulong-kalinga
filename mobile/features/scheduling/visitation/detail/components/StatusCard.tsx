import { CheckCircle, Calendar, XCircle } from "lucide-react-native";
import {
    Card,
    H4,
    Text,
    XStack,
    YStack,
} from "tamagui";

interface Props {
    beneficiaryConfirmed: boolean;
    familyConfirmed: boolean;
    confirmedOn?: string;
}

const StatusCard = ({
    beneficiaryConfirmed,
    familyConfirmed,
    confirmedOn,
}: Props) => {
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
                    <CheckCircle
                        size={24}
                        color="#3b82f6"
                    />
                    <H4 color="#111827">
                        Confirmation Status
                    </H4>
                </XStack>

                <YStack gap="$3">
                    <XStack
                        style={{
                            backgroundColor: "#f3f4f6",
                            padding: 12,
                            borderRadius: 8,
                            justifyContent: "space-between",
                            alignItems: "center",
                        }}
                    >
                        <Text
                            fontSize="$4"
                            style={{
                                color: "#6b7280",
                            }}
                        >
                            Beneficiary:
                        </Text>
                        <XStack
                            style={{
                                alignItems: "center",
                            }}
                            gap="$2"
                        >
                            {beneficiaryConfirmed ? (
                                <CheckCircle
                                    size={16}
                                    color="#22c55e"
                                />
                            ) : (
                                <XCircle
                                    size={16}
                                    color="#ef4444"
                                />
                            )}
                            <Text
                                fontSize="$4"
                                fontWeight="500"
                                style={{
                                    color: beneficiaryConfirmed
                                        ? "#22c55e"
                                        : "#ef4444",
                                }}
                            >
                                {beneficiaryConfirmed
                                    ? "Confirmed"
                                    : "Not Confirmed"}
                            </Text>
                        </XStack>
                    </XStack>

                    <XStack
                        style={{
                            backgroundColor: "#f3f4f6",
                            padding: 12,
                            borderRadius: 8,
                            justifyContent: "space-between",
                            alignItems: "center",
                        }}
                    >
                        <Text
                            fontSize="$4"
                            style={{
                                color: "#6b7280",
                            }}
                        >
                            Family:
                        </Text>
                        <XStack
                            style={{
                                alignItems: "center",
                            }}
                            gap="$2"
                        >
                            {familyConfirmed ? (
                                <CheckCircle
                                    size={16}
                                    color="#22c55e"
                                />
                            ) : (
                                <XCircle
                                    size={16}
                                    color="#ef4444"
                                />
                            )}
                            <Text
                                fontSize="$4"
                                fontWeight="500"
                                style={{
                                    color: familyConfirmed
                                        ? "#22c55e"
                                        : "#ef4444",
                                }}
                            >
                                {familyConfirmed
                                    ? "Confirmed"
                                    : "Not Confirmed"}
                            </Text>
                        </XStack>
                    </XStack>

                    {confirmedOn && (
                        <XStack
                            style={{
                                backgroundColor: "#f0f9ff",
                                padding: 12,
                                borderRadius: 8,
                                justifyContent: "space-between",
                                alignItems: "center",
                            }}
                        >
                            <XStack
                                style={{
                                    alignItems: "center",
                                }}
                                gap="$2"
                            >
                                <Calendar
                                    size={16}
                                    color="#0369a1"
                                />
                                <Text
                                    fontSize="$4"
                                    style={{
                                        color: "#0369a1",
                                    }}
                                    fontWeight="500"
                                >
                                    Confirmed On:
                                </Text>
                            </XStack>
                            <Text
                                fontSize="$4"
                                style={{
                                    color: "#0369a1",
                                }}
                                fontWeight="600"
                            >
                                {confirmedOn}
                            </Text>
                        </XStack>
                    )}
                </YStack>
            </YStack>
        </Card>
    );
};

export default StatusCard;
