import { AlertCircle, RefreshCw } from "lucide-react-native";
import { Button, Text, YStack } from "tamagui";

interface Props {
    onRetry?: () => void;
    message?: string;
}

const ErrorState = ({ 
    onRetry, 
    message = "Unable to load visitations. Please try again." 
}: Props) => {
    return (
        <YStack
            flex={1}
            justifyContent="center"
            alignItems="center"
            gap="$4"
            p="$6"
        >
            <AlertCircle size={64} color="$red10" />
            <YStack alignItems="center" gap="$2">
                <Text
                    fontSize="$6"
                    fontWeight="600"
                    color="$red11"
                    textAlign="center"
                >
                    Something went wrong
                </Text>
                <Text
                    fontSize="$4"
                    color="$gray10"
                    textAlign="center"
                    maxWidth={280}
                >
                    {message}
                </Text>
            </YStack>
            
            {onRetry && (
                <Button
                    size="$4"
                    theme="red"
                    onPress={onRetry}
                    icon={RefreshCw}
                    borderRadius="$4"
                >
                    Try Again
                </Button>
            )}
        </YStack>
    );
};

export default ErrorState;
