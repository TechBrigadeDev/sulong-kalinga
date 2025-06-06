import { Pill } from "lucide-react-native";
import {
    Card,
    H4,
    Text,
    XStack,
    YStack,
} from "tamagui";

import Badge from "~/components/Bagde";

interface HeaderCardProps {
    medication_name?: string;
    dosage?: string;
    medication_type?: string;
    status?: string;
}

const getStatusVariant = (status?: string) => {
    switch (status) {
        case "active":
            return "success";
        case "paused":
            return "warning";
        case "completed":
            return "ghost";
        default:
            return "default";
    }
};

const HeaderCard = (props: HeaderCardProps) => {
    const {
        medication_name,
        dosage,
        medication_type,
        status,
    } = props || {};
    return (
        <Card mb="$2">
            <Card.Header padded>
                <YStack gap="$2">
                    <XStack
                        items="center"
                        gap="$2"
                    >
                        <H4>{medication_name}</H4>
                        <Badge
                            variant={getStatusVariant(
                                status,
                            )}
                            style={{
                                marginLeft:
                                    "auto",
                            }}
                        >
                            {status}
                        </Badge>
                    </XStack>
                    <XStack gap="$4">
                        <XStack
                            flex={1}
                            gap="$2"
                            items="center"
                        >
                            <Pill size={16} />
                            <Text>{dosage}</Text>
                        </XStack>
                        <Text>
                            {medication_type}
                        </Text>
                    </XStack>
                </YStack>
            </Card.Header>
        </Card>
    );
};

export default HeaderCard;
