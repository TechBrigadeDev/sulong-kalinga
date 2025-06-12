import {
    Calendar,
    Clock,
} from "@tamagui/lucide-icons";
import Badge from "components/Bagde";
import {
    Card,
    H6,
    Paragraph,
    XStack,
    YStack,
} from "tamagui";

interface Props {
    date: string;
    time: string;
    type: string;
    status: string;
}

const VisitDetailsCard = ({
    date,
    time,
    type,
    status,
}: Props) => {
    return (
        <Card elevate bordered p="$4" mb="$2">
            <YStack space="$4">
                <H6>Visit Details</H6>
                <YStack space="$2">
                    <XStack space="$2">
                        <Calendar size="$1" />
                        <Paragraph>
                            Date: {date}
                        </Paragraph>
                    </XStack>
                    <XStack space="$2">
                        <Clock size="$1" />
                        <Paragraph>
                            Time: {time}
                        </Paragraph>
                    </XStack>
                    <Paragraph>
                        Type: {type}
                    </Paragraph>
                    <Badge>{status}</Badge>
                </YStack>
            </YStack>
        </Card>
    );
};

export default VisitDetailsCard;
