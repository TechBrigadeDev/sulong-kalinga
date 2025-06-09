import {
    Card,
    H6,
    Paragraph,
    YStack,
} from "tamagui";

interface Props {
    location: string;
}

const LocationCard = ({ location }: Props) => {
    return (
        <Card bordered p="$4" mb="$2">
            <YStack gap="$2">
                <H6>Location</H6>
                <Paragraph>{location}</Paragraph>
            </YStack>
        </Card>
    );
};

export default LocationCard;
