import { FileText } from "lucide-react-native";
import {
    Card,
    H4,
    Text,
    XStack,
    YStack,
} from "tamagui";

interface Props {
    notes: string;
}

const NotesCard = ({ notes }: Props) => {
    if (!notes) return null;

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
                    <FileText
                        size={24}
                        color="#3b82f6"
                    />
                    <H4 color="#111827">
                        Notes
                    </H4>
                </XStack>

                <YStack
                    style={{
                        backgroundColor: "#fed7aa",
                        padding: 12,
                        borderRadius: 8,
                        borderLeftWidth: 3,
                        borderLeftColor: "#ea580c",
                    }}
                >
                    <Text
                        fontSize="$4"
                        style={{
                            color: "#ea580c",
                        }}
                        lineHeight="$1"
                    >
                        {notes}
                    </Text>
                </YStack>
            </YStack>
        </Card>
    );
};

export default NotesCard;
