import { MapPin } from "lucide-react-native";
import {
    Card,
    H4,
    Text,
    XStack,
    YStack,
} from "tamagui";

interface Props {
    location: string;
}

const LocationCard = ({ location }: Props) => {
    return (
        <Card
            elevate
            mb="$4"
            p="$4"
            style={{ borderRadius: 16 }}
        >
            <YStack gap="$3">
                <XStack
                    style={{
                        alignItems: "center",
                    }}
                    gap="$2"
                >
                    <MapPin
                        size={24}
                        color="#3b82f6"
                    />
                    <H4 color="#111827">
                        Location
                    </H4>
                </XStack>

                <YStack
                    style={{
                        backgroundColor:
                            "#f3f4f6",
                        padding: 12,
                        borderRadius: 8,
                    }}
                >
                    <Text
                        fontSize="$4"
                        fontWeight="500"
                        color="#111827"
                    >
                        {location}
                    </Text>
                </YStack>
            </YStack>
        </Card>
    );
};

export default LocationCard;
