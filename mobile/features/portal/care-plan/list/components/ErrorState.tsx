import {
    AlertCircle,
    RefreshCw,
} from "lucide-react-native";
import { Button, Text, YStack } from "tamagui";

interface Props {
    onRetry?: () => void;
    message?: string;
}

const ErrorState = ({
    onRetry,
    message = "Unable to load care plans. Please check your connection and try again.",
}: Props) => {
    return (
        <YStack
            flex={1}
            style={{
                justifyContent: "center",
                alignItems: "center",
            }}
            gap="$4"
            p="$4"
        >
            <AlertCircle
                size={64}
                color="#ef4444"
            />
            <YStack
                style={{
                    alignItems: "center",
                }}
                gap="$2"
            >
                <Text
                    fontSize="$6"
                    fontWeight="600"
                    color="#dc2626"
                    style={{
                        textAlign: "center",
                    }}
                >
                    Something went wrong
                </Text>
                <Text
                    fontSize="$4"
                    color="#6b7280"
                    style={{
                        textAlign: "center",
                        maxWidth: 280,
                    }}
                >
                    {message}
                </Text>
            </YStack>

            {onRetry && (
                <Button
                    size="$4"
                    theme="red"
                    onPress={onRetry}
                    icon={<RefreshCw size={16} />}
                >
                    Try Again
                </Button>
            )}
        </YStack>
    );
};

export default ErrorState;
