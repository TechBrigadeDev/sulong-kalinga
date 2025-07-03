import { UserCheck } from "lucide-react-native";
import {
    Card,
    H4,
    Text,
    XStack,
    YStack,
} from "tamagui";

interface Props {
    name: string;
}

const CareWorkerCard = ({ name }: Props) => {
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
                    <UserCheck
                        size={24}
                        color="#3b82f6"
                    />
                    <H4 color="#111827">
                        Care Worker
                    </H4>
                </XStack>

                <YStack
                    style={{
                        backgroundColor: "#f3f4f6",
                        padding: 12,
                        borderRadius: 8,
                    }}
                >
                    <Text
                        fontSize="$4"
                        fontWeight="500"
                        color="#111827"
                    >
                        {name}
                    </Text>
                </YStack>
            </YStack>
        </Card>
    );
};

export default CareWorkerCard;
