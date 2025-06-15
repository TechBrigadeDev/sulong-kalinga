import {
    formatDate,
    formatDuration,
    formatTime,
    getRelativeDate,
} from "common/date";
import ScrollView from "components/ScrollView";
import { Stack } from "expo-router";
import { MedicationSchedule } from "features/portal/medication/list/types";
import {
    AlertTriangle,
    Calendar,
    Clock,
    Info,
    Pill,
} from "lucide-react-native";
import {
    Card,
    H3,
    H4,
    Text,
    XStack,
    YStack,
} from "tamagui";

interface Props {
    medication: MedicationSchedule;
}

const PortalMedicationDetail = ({
    medication,
}: Props) => {
    return (
        <>
            <Stack.Screen
                options={{
                    headerTitle:
                        medication.medication_name,
                }}
            />
            <ScrollView
                flex={1}
                style={{
                    backgroundColor: "#f9fafb",
                }}
                contentContainerStyle={{
                    paddingBlockEnd: 110,
                }}
            >
                <YStack gap="$4" p="$4">
                    <MedicationInfoCard
                        medication={medication}
                    />
                    <ScheduleCard
                        medication={medication}
                    />
                    <DurationCard
                        medication={medication}
                    />
                    {medication.special_instructions && (
                        <SpecialInstructionsCard
                            medication={
                                medication
                            }
                        />
                    )}
                </YStack>
            </ScrollView>
        </>
    );
};

const MedicationInfoCard = ({
    medication,
}: Props) => {
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
                    gap="$3"
                >
                    <Pill
                        size={32}
                        color="#3b82f6"
                    />
                    <YStack flex={1}>
                        <H3
                            color="#111827"
                            numberOfLines={2}
                        >
                            {
                                medication.medication_name
                            }
                        </H3>
                        <Text
                            fontSize="$4"
                            style={{
                                color: "#6b7280",
                            }}
                        >
                            {
                                medication.medication_type
                            }
                        </Text>
                    </YStack>
                </XStack>

                <XStack
                    style={{
                        backgroundColor:
                            "#3b82f6",
                        paddingHorizontal: 12,
                        paddingVertical: 8,
                        borderRadius: 8,
                    }}
                >
                    <Text
                        fontSize="$3"
                        color="white"
                        fontWeight="600"
                        textTransform="capitalize"
                    >
                        {medication.status}
                    </Text>
                </XStack>

                <YStack
                    style={{
                        backgroundColor:
                            "#dbeafe",
                        padding: 12,
                        borderRadius: 8,
                        alignItems: "center",
                    }}
                    gap="$2"
                >
                    <Info
                        size={20}
                        color="#3b82f6"
                    />
                    <Text
                        fontSize="$4"
                        style={{
                            color: "#1e40af",
                        }}
                        fontWeight="500"
                    >
                        Dosage:{" "}
                        {medication.dosage}
                    </Text>
                </YStack>
            </YStack>
        </Card>
    );
};

const ScheduleCard = ({ medication }: Props) => {
    const schedules = [
        {
            time: "Morning",
            value: medication.morning_time,
            withFood:
                medication.with_food_morning,
        },
        {
            time: "Noon",
            value: medication.noon_time,
            withFood: medication.with_food_noon,
        },
        {
            time: "Evening",
            value: medication.evening_time,
            withFood:
                medication.with_food_evening,
        },
        {
            time: "Night",
            value: medication.night_time,
            withFood: medication.with_food_night,
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
                                                schedule.value,
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
                ) : medication.as_needed ? (
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
                        }}
                        text="center"
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

const DurationCard = ({ medication }: Props) => {
    const startDate = formatDate(
        medication.start_date,
        "MMMM dd, yyyy",
    );
    const endDate = medication.end_date
        ? formatDate(
              medication.end_date,
              "MMMM dd, yyyy",
          )
        : null;

    const duration = formatDuration(
        medication.start_date,
        medication.end_date,
    );

    const relativeStartDate = getRelativeDate(
        medication.start_date,
    );
    const relativeEndDate = medication.end_date
        ? getRelativeDate(medication.end_date)
        : null;

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
                    <Calendar
                        size={24}
                        color="#3b82f6"
                    />
                    <H4 color="#111827">
                        Duration
                    </H4>
                </XStack>

                <YStack gap="$3">
                    <XStack
                        style={{
                            justifyContent:
                                "space-between",
                        }}
                    >
                        <Text
                            fontSize="$4"
                            style={{
                                color: "#6b7280",
                            }}
                        >
                            Start Date:
                        </Text>
                        <YStack
                            style={{
                                alignItems:
                                    "flex-end",
                            }}
                        >
                            <Text
                                fontSize="$4"
                                fontWeight="500"
                            >
                                {startDate}
                            </Text>
                            <Text
                                fontSize="$3"
                                style={{
                                    color: "#3b82f6",
                                }}
                            >
                                {
                                    relativeStartDate
                                }
                            </Text>
                        </YStack>
                    </XStack>

                    <XStack
                        style={{
                            justifyContent:
                                "space-between",
                        }}
                    >
                        <Text
                            fontSize="$4"
                            style={{
                                color: "#6b7280",
                            }}
                        >
                            End Date:
                        </Text>
                        <YStack
                            style={{
                                alignItems:
                                    "flex-end",
                            }}
                        >
                            <Text
                                fontSize="$4"
                                fontWeight="500"
                            >
                                {endDate ||
                                    "Ongoing"}
                            </Text>
                            {relativeEndDate && (
                                <Text
                                    fontSize="$3"
                                    style={{
                                        color: "#3b82f6",
                                    }}
                                >
                                    {
                                        relativeEndDate
                                    }
                                </Text>
                            )}
                        </YStack>
                    </XStack>

                    <XStack
                        style={{
                            backgroundColor:
                                "#f0f9ff",
                            padding: 12,
                            borderRadius: 8,
                            justifyContent:
                                "space-between",
                            alignItems: "center",
                        }}
                    >
                        <Text
                            fontSize="$4"
                            style={{
                                color: "#0369a1",
                            }}
                            fontWeight="500"
                        >
                            Duration:
                        </Text>
                        <Text
                            fontSize="$4"
                            style={{
                                color: "#0369a1",
                            }}
                            fontWeight="600"
                        >
                            {medication.end_date
                                ? duration
                                : `${duration} (ongoing)`}
                        </Text>
                    </XStack>
                </YStack>
            </YStack>
        </Card>
    );
};

const SpecialInstructionsCard = ({
    medication,
}: Props) => {
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
                    <AlertTriangle
                        size={24}
                        color="#ea580c"
                    />
                    <H4 color="#111827">
                        Special Instructions
                    </H4>
                </XStack>

                <YStack
                    style={{
                        backgroundColor:
                            "#fed7aa",
                        padding: 12,
                        borderRadius: 8,
                        borderLeftWidth: 3,
                        borderLeftColor:
                            "#ea580c",
                    }}
                >
                    <Text
                        fontSize="$4"
                        style={{
                            color: "#ea580c",
                        }}
                        lineHeight="$1"
                    >
                        {
                            medication.special_instructions
                        }
                    </Text>
                </YStack>
            </YStack>
        </Card>
    );
};

export default PortalMedicationDetail;
