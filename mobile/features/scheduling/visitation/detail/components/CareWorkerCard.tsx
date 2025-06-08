import {
    Card,
    H6,
    Paragraph,
    YStack,
} from "tamagui";

interface Props {
    name: string;
}

const CareWorkerCard = ({ name }: Props) => {
    return (
        <Card bordered p="$4" mb="$2">
            <YStack gap="$2">
                <H6>Care Worker</H6>
                <Paragraph>{name}</Paragraph>
            </YStack>
        </Card>
    );
};

export default CareWorkerCard;
