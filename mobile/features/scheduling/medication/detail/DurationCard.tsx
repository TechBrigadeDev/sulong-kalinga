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
    const formatDate = (date?: string) => {
        if (!date) return "";
        return new Date(
            date,
        ).toLocaleDateString();
    };

    return (
        <Card mb="$2">
            <Card.Header padded>
                <XStack gap="$2" items="center">
                    <Calendar size={16} />
                    <H4>Duration</H4>
                </XStack>
            </Card.Header>
            <YStack p="$4">
                <YStack gap="$2">
                    <XStack gap="$2">
                        <Text opacity={0.6}>
                            Start Date:
                        </Text>
                        <Text>
                            {formatDate(
                                start_date,
                            )}
                        </Text>
                    </XStack>
                    {end_date && (
                        <XStack gap="$2">
                            <Text opacity={0.6}>
                                End Date:
                            </Text>
                            <Text>
                                {formatDate(
                                    end_date,
                                )}
                            </Text>
                        </XStack>
                    )}
                </YStack>
            </YStack>
        </Card>
    );
};

export default DurationCard;
