import { formatTime } from "common/date";
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

    const schedules = [
        {
            time: "Morning",
            value: morning_time,
            withFood: with_food_morning,
        },
        {
            time: "Noon",
            value: noon_time,
            withFood: with_food_noon,
        },
        {
            time: "Evening",
            value: evening_time,
            withFood: with_food_evening,
        },
        {
            time: "Night",
            value: night_time,
            withFood: with_food_night,
        },
    ];

    const activeSchedules = schedules.filter(
        (schedule) => schedule.value,
    );

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
                    <Clock
                        size={24}
                        color="#3b82f6"
                    />
                    <H4 color="#111827">
                        Schedule
                    </H4>
                </XStack>

                {activeSchedules.length > 0 ? (
                    <YStack gap="$3">
                        {activeSchedules.map(
                            (schedule, index) => (
                                <XStack
                                    key={index}
                                    style={{
                                        backgroundColor:
                                            "#f3f4f6",
                                        padding: 12,
                                        borderRadius: 8,
                                        justifyContent:
                                            "space-between",
                                        alignItems:
                                            "center",
                                    }}
                                >
                                    <YStack gap="$1">
                                        <Text
                                            fontSize="$4"
                                            fontWeight="600"
                                        >
                                            {
                                                schedule.time
                                            }
                                        </Text>
                                        <Text
                                            fontSize="$5"
                                            style={{
                                                color: "#3b82f6",
                                            }}
                                            fontWeight="500"
                                        >
                                            {formatTime(
                                                schedule.value!,
                                            )}
                                        </Text>
                                    </YStack>
                                    {schedule.withFood && (
                                        <XStack
                                            style={{
                                                backgroundColor:
                                                    "#fed7aa",
                                                paddingHorizontal: 8,
                                                paddingVertical: 4,
                                                borderRadius: 6,
                                                alignItems:
                                                    "center",
                                            }}
                                            gap="$1"
                                        >
                                            <Text
                                                fontSize="$2"
                                                style={{
                                                    color: "#ea580c",
                                                }}
                                            >
                                                With
                                                food
                                            </Text>
                                        </XStack>
                                    )}
                                </XStack>
                            ),
                        )}
                    </YStack>
                ) : as_needed ? (
                    <YStack
                        style={{
                            backgroundColor:
                                "#fef3c7",
                            padding: 12,
                            borderRadius: 8,
                            alignItems: "center",
                        }}
                        gap="$2"
                    >
                        <AlertTriangle
                            size={16}
                            color="#f59e0b"
                        />
                        <Text
                            fontSize="$4"
                            style={{
                                color: "#d97706",
                            }}
                            fontWeight="500"
                        >
                            As needed basis
                        </Text>
                    </YStack>
                ) : (
                    <Text
                        fontSize="$4"
                        style={{
                            color: "#6b7280",
                            textAlign: "center",
                        }}
                        py="$4"
                    >
                        No specific schedule times
                        set
                    </Text>
                )}
            </YStack>
        </Card>
    );
};

export default ScheduleCard;
