import { FileText } from "lucide-react-native";
import { Card, H4, Text, XStack } from "tamagui";

interface SpecialInstructionsCardProps {
    special_instructions?: string;
}

const SpecialInstructionsCard = (
    props: SpecialInstructionsCardProps,
) => {
    const { special_instructions } = props || {};
    if (!special_instructions) return null;

    return (
        <Card mb="$2">
            <Card.Header padded>
                <XStack gap="$2" items="center">
                    <FileText size={16} />
                    <H4>Special Instructions</H4>
                </XStack>
            </Card.Header>
            <YStack p="$4">
                <Text>
                    {special_instructions}
                </Text>
            </YStack>
        </Card>
    );
};

export default SpecialInstructionsCard;
