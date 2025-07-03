import { Info, Pill } from "lucide-react-native";
import {
    Card,
    H3,
    Text,
    XStack,
    YStack,
} from "tamagui";

interface HeaderCardProps {
    medication_name?: string;
    dosage?: string;
    medication_type?: string;
    status?: string;
}

const HeaderCard = (props: HeaderCardProps) => {
    const {
        medication_name,
        dosage,
        medication_type,
        status,
    } = props || {};

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
                    gap="$3"
                >
                    <Pill
                        size={32}
                        color="#3b82f6"
                    />
                    <YStack flex={1}>
                        <H3
                            color="#111827"
                            numberOfLines={2}
                        >
                            {medication_name}
                        </H3>
                        <Text
                            fontSize="$4"
                            style={{
                                color: "#6b7280",
                            }}
                        >
                            {medication_type}
                        </Text>
                    </YStack>
                </XStack>

                <XStack
                    style={{
                        backgroundColor:
                            "#3b82f6",
                        paddingHorizontal: 12,
                        paddingVertical: 8,
                        borderRadius: 8,
                    }}
                >
                    <Text
                        fontSize="$3"
                        color="white"
                        fontWeight="600"
                        textTransform="capitalize"
                    >
                        {status}
                    </Text>
                </XStack>

                <YStack
                    style={{
                        backgroundColor:
                            "#dbeafe",
                        padding: 12,
                        borderRadius: 8,
                        alignItems: "center",
                    }}
                    gap="$2"
                >
                    <Info
                        size={20}
                        color="#3b82f6"
                    />
                    <Text
                        fontSize="$4"
                        style={{
                            color: "#1e40af",
                        }}
                        fontWeight="500"
                    >
                        Dosage: {dosage}
                    </Text>
                </YStack>
            </YStack>
        </Card>
    );
};

export default HeaderCard;
