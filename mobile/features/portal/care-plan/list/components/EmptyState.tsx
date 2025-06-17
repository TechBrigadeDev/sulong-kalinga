import {
    FileX,
    Search,
} from "lucide-react-native";
import { Button, Text, YStack } from "tamagui";

interface Props {
    hasSearch: boolean;
    searchTerm?: string;
    onClearSearch?: () => void;
}

const EmptyState = ({
    hasSearch,
    searchTerm,
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
                <FileX size={64} color="$gray8" />
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
                        : "No care plans found"}
                </Text>
                <Text
                    fontSize="$4"
                    color="$gray9"
                    textAlign="center"
                    maxWidth={280}
                    lineHeight="$1"
                >
                    {hasSearch
                        ? `No care plans match "${searchTerm}". Try adjusting your search terms.`
                        : "You don't have any care plan records at the moment."}
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
