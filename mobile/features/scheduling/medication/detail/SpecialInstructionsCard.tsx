import { AlertTriangle } from "lucide-react-native";
import {
    Card,
    H4,
    Text,
    XStack,
    YStack,
} from "tamagui";

interface SpecialInstructionsCardProps {
    special_instructions?: string;
}

const SpecialInstructionsCard = (
    props: SpecialInstructionsCardProps,
) => {
    const { special_instructions } = props || {};
    if (!special_instructions) return null;

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
                    <AlertTriangle
                        size={24}
                        color="#ea580c"
                    />
                    <H4 color="#111827">
                        Special Instructions
                    </H4>
                </XStack>

                <YStack
                    style={{
                        backgroundColor:
                            "#fed7aa",
                        padding: 12,
                        borderRadius: 8,
                        borderLeftWidth: 3,
                        borderLeftColor:
                            "#ea580c",
                    }}
                >
                    <Text
                        fontSize="$4"
                        style={{
                            color: "#ea580c",
                        }}
                        lineHeight="$1"
                    >
                        {special_instructions}
                    </Text>
                </YStack>
            </YStack>
        </Card>
    );
};

export default SpecialInstructionsCard;
