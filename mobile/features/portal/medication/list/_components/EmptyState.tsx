import {
    Pill,
    Search,
} from "lucide-react-native";
import { Button, Text, YStack } from "tamagui";

interface Props {
    hasSearch: boolean;
    onClearSearch?: () => void;
}

const EmptyState = ({
    hasSearch,
    onClearSearch,
}: Props) => {
    return (
        <YStack
            flex={1}
            justifyContent="center"
            alignItems="center"
            gap="$4"
            p="$6"
        >
            {hasSearch ? (
                <Search
                    size={64}
                    color="$gray8"
                />
            ) : (
                <Pill size={64} color="$gray8" />
            )}

            <YStack alignItems="center" gap="$2">
                <Text
                    fontSize="$6"
                    fontWeight="600"
                    color="$gray11"
                    textAlign="center"
                >
                    {hasSearch
                        ? "No results found"
                        : "No medications found"}
                </Text>
                <Text
                    fontSize="$4"
                    color="$gray9"
                    textAlign="center"
                    maxWidth={280}
                    lineHeight="$1"
                >
                    {hasSearch
                        ? "Try adjusting your search terms to find medications."
                        : "You don't have any medication schedules at the moment."}
                </Text>
            </YStack>

            {hasSearch && onClearSearch && (
                <Button
                    size="$4"
                    variant="outlined"
                    onPress={onClearSearch}
                    theme="blue"
                >
                    Clear Search
                </Button>
            )}
        </YStack>
    );
};

export default EmptyState;
