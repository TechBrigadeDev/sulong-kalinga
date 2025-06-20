import { Text, YStack } from "tamagui";

export const FormErrors = ({
    errors,
}: {
    errors: { message: string }[];
}) => {
    if (errors.length === 0) {
        return null;
    }
    return (
        <YStack px="$2" gap="$1">
            {errors.map((error, index) => (
                <Text
                    key={index}
                    color="$red10"
                    fontSize="$2"
                >
                    • {error.message}
                </Text>
            ))}
        </YStack>
    );
};
