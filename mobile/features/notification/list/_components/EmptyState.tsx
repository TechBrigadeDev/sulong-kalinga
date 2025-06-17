import { Bell } from "lucide-react-native";
import { Text, YStack } from "tamagui";

const EmptyState = () => {
    return (
        <YStack
            flex={1}
            gap="$4"
            p="$6"
            style={{
                justifyContent: "center",
                alignItems: "center",
            }}
        >
            <Bell size={64} color="#9ca3af" />

            <YStack
                gap="$2"
                style={{
                    alignItems: "center",
                }}
            >
                <Text
                    fontSize="$6"
                    fontWeight="600"
                    color="#374151"
                    style={{
                        textAlign: "center",
                    }}
                >
                    No notifications
                </Text>
                <Text
                    fontSize="$4"
                    color="#6b7280"
                    style={{
                        textAlign: "center",
                        maxWidth: 280,
                        lineHeight: 20,
                    }}
                >
                    You don&apos;t have any
                    notifications at the moment.
                </Text>
            </YStack>
        </YStack>
    );
};

export default EmptyState;
