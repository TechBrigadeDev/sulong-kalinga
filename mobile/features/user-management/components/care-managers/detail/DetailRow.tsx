import { Text, XStack } from "tamagui";

interface DetailRowProps {
    label: string;
    value: string | number | null;
}

const DetailRow = ({ label, value }: DetailRowProps) => (
    <XStack gap="$2">
        <Text opacity={0.6} flex={1} fontSize="$4">
            {label}:
        </Text>
        <Text flex={2} fontSize="$4">
            {value || "N/A"}
        </Text>
    </XStack>
);

export default DetailRow;
