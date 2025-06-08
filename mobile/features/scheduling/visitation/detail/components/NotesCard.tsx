import {
    Card,
    H6,
    Paragraph,
    YStack,
} from "tamagui";

interface Props {
    notes: string;
}

const NotesCard = ({ notes }: Props) => {
    return (
        <Card bordered p="$4" mb="$2">
            <YStack space="$2">
                <H6>Notes</H6>
                <Paragraph>{notes}</Paragraph>
            </YStack>
        </Card>
    );
};

export default NotesCard;
