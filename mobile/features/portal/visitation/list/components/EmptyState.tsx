import { Calendar } from "lucide-react-native";
import { Text, YStack } from "tamagui";

const EmptyState = () => {
    return (
        <YStack
            flex={1}
            justifyContent="center"
            alignItems="center"
            gap="$4"
            p="$6"
        >
            <Calendar size={64} color="$gray8" />
            <YStack alignItems="center" gap="$2">
                <Text
                    fontSize="$6"
                    fontWeight="600"
                    color="$gray11"
                    textAlign="center"
                >
                    No Visitations Scheduled
                </Text>
                <Text
                    fontSize="$4"
                    color="$gray10"
                    textAlign="center"
                    maxWidth={280}
                >
                    You don't have any visitations scheduled at the moment.
                </Text>
            </YStack>
        </YStack>
    );
};

export default EmptyState;
