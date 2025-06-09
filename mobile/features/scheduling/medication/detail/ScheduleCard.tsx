import {
    AlertTriangle,
    Clock,
} from "lucide-react-native";
import {
    Card,
    H4,
    Text,
    XStack,
    YStack,
} from "tamagui";

interface ScheduleCardProps {
    morning_time?: string;
    noon_time?: string;
    evening_time?: string;
    night_time?: string;
    with_food_morning?: boolean;
    with_food_noon?: boolean;
    with_food_evening?: boolean;
    with_food_night?: boolean;
    as_needed?: boolean;
}

const ScheduleCard = (
    props: ScheduleCardProps,
) => {
    const {
        morning_time,
        noon_time,
        evening_time,
        night_time,
        with_food_morning,
        with_food_noon,
        with_food_evening,
        with_food_night,
        as_needed,
    } = props || {};
    const formatTime = (time?: string) => {
        if (!time) return "";
        return new Date(time).toLocaleTimeString(
            [],
            {
                hour: "numeric",
                minute: "numeric",
            },
        );
    };

    return (
        <Card mb="$2">
            <Card.Header padded>
                <XStack gap="$2" items="center">
                    <Clock size={16} />
                    <H4>Schedule</H4>
                </XStack>
            </Card.Header>
            <YStack p="$4">
                <YStack gap="$3">
                    {as_needed ? (
                        <XStack
                            gap="$2"
                            items="center"
                        >
                            <AlertTriangle
                                size={16}
                                color="#facc15"
                            />
                            <Text>
                                Take as needed
                            </Text>
                        </XStack>
                    ) : (
                        <>
                            {morning_time && (
                                <XStack
                                    display="flex"
                                    gap="$2"
                                >
                                    <Text>
                                        Morning
                                    </Text>
                                    <XStack gap="$2">
                                        <Text>
                                            {formatTime(
                                                morning_time,
                                            )}
                                        </Text>
                                        {with_food_morning && (
                                            <Text color="gray">
                                                (with
                                                food)
                                            </Text>
                                        )}
                                    </XStack>
                                </XStack>
                            )}
                            {noon_time && (
                                <XStack
                                    display="flex"
                                    gap="$2"
                                >
                                    <Text>
                                        Noon
                                    </Text>
                                    <XStack gap="$2">
                                        <Text>
                                            {formatTime(
                                                noon_time,
                                            )}
                                        </Text>
                                        {with_food_noon && (
                                            <Text color="gray">
                                                (with
                                                food)
                                            </Text>
                                        )}
                                    </XStack>
                                </XStack>
                            )}
                            {evening_time && (
                                <XStack
                                    display="flex"
                                    gap="$2"
                                >
                                    <Text>
                                        Evening
                                    </Text>
                                    <XStack gap="$2">
                                        <Text>
                                            {formatTime(
                                                evening_time,
                                            )}
                                        </Text>
                                        {with_food_evening && (
                                            <Text color="gray">
                                                (with
                                                food)
                                            </Text>
                                        )}
                                    </XStack>
                                </XStack>
                            )}
                            {night_time && (
                                <XStack
                                    display="flex"
                                    gap="$2"
                                >
                                    <Text>
                                        Night
                                    </Text>
                                    <XStack gap="$2">
                                        <Text>
                                            {formatTime(
                                                night_time,
                                            )}
                                        </Text>
                                        {with_food_night && (
                                            <Text color="gray">
                                                (with
                                                food)
                                            </Text>
                                        )}
                                    </XStack>
                                </XStack>
                            )}
                        </>
                    )}
                </YStack>
            </YStack>
        </Card>
    );
};

export default ScheduleCard;
