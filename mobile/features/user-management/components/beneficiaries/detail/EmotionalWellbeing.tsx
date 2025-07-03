import { Smile } from "lucide-react-native";
import {
    Card,
    H3,
    H4,
    Text,
    XStack,
    YStack,
} from "tamagui";

import { IBeneficiary } from "~/features/user-management/management.type";

interface Props {
    beneficiary: IBeneficiary;
}

const EmotionalWellbeing = ({
    beneficiary: _beneficiary,
}: Props) => {
    const emotionalInfo = {
        Mood: "Expedita sint nemo quaerat qui dignissimos consequatur consequatur.",
        "Social Interactions":
            "Quas similique est voluptatum est sunt similique.",
        "Emotional Support Need":
            "Nam cumque et delectus qui tempore nam.",
    };

    return (
        <Card
            elevate
            mb="$4"
            style={{ borderRadius: 16 }}
            backgroundColor="$background"
            borderColor="$borderColor"
        >
            <YStack gap="$4" p="$4">
                <XStack
                    gap="$3"
                    style={{
                        alignItems: "center",
                    }}
                >
                    <Smile
                        size={24}
                        color="#f59e0b"
                    />
                    <H4
                        color="#111827"
                        fontWeight="600"
                    >
                        Emotional Well-being
                    </H4>
                </XStack>
                <YStack gap="$4">
                    {Object.entries(
                        emotionalInfo,
                    ).map(([key, value]) => (
                        <YStack key={key}>
                            <Text
                                fontSize="$3"
                                style={{
                                    color: "#6b7280",
                                }}
                                fontWeight="500"
                                mb="$2"
                            >
                                {key}
                            </Text>
                            <Text
                                fontSize="$4"
                                style={{
                                    color: "#111827",
                                }}
                                fontWeight="400"
                            >
                                {value}
                            </Text>
                        </YStack>
                    ))}
                </YStack>
            </YStack>
        </Card>
    );
};

export default EmotionalWellbeing;
