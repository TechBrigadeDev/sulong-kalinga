import { Input, Label, YStack } from "tamagui";

export const ReviewDate = () => {
    return (
        <YStack gap="$2">
            <Label fontWeight="600">
                Review Date
            </Label>
            <Input
                size="$4"
                editable={false}
                value={new Date().toLocaleDateString()}
            />
        </YStack>
    );
};
