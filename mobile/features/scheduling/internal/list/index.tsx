import { formatDate } from "common/date";
import { QK, setDataQK } from "common/query";
import { weekCalendarStore } from "components/calendars/WeekCalendar/store";
import FlatList from "components/FlatList";
import { useRouter } from "expo-router";
import { useInternalSchedules } from "features/scheduling/internal/hook";
import { IInternalAppointment } from "features/scheduling/internal/type";
import {
    BookUser,
    Calendar,
    Clock,
    Eye,
    MapPin,
} from "lucide-react-native";
import { useCallback, useMemo } from "react";
import {
    Button,
    Card,
    Text,
    XStack,
    YStack,
} from "tamagui";

const InternalScheduleList = () => {
    const { data, isLoading } =
        useInternalSchedules();

    if (!data || isLoading) {
        return null;
    }

    return (
        <FlatList<IInternalAppointment>
            data={data}
            renderItem={({ item }) => (
                <Schedule appointment={item} />
            )}
        />
    );
};

const Schedule = ({
    appointment,
}: {
    appointment: IInternalAppointment;
}) => {
    const router = useRouter();
    const { date } = weekCalendarStore();

    const currentStatus = useMemo(() => {
        if (
            appointment.occurrences.length ===
                0 ||
            !date
        ) {
            return appointment.status;
        }
        const occurrence =
            appointment.occurrences.find(
                (occurrence) =>
                    new Date(
                        occurrence.occurrence_date,
                    ).toDateString() ===
                    date.toDateString(),
            ) || appointment.occurrences[0];

        return (
            occurrence?.status ||
            appointment.status
        );
    }, [
        appointment.occurrences,
        appointment.status,
        date,
    ]);

    const bgColor = useMemo(() => {
        switch (currentStatus) {
            case "completed":
                return "#10b981"; // green
            case "canceled":
                return "#ef4444"; // red
            case "scheduled":
                return "#3b82f6"; // blue
            default:
                return "#6b7280"; // gray
        }
    }, [currentStatus]);

    const textColor = useMemo(() => {
        switch (currentStatus) {
            case "completed":
            case "canceled":
            case "scheduled":
                return "white";
            default:
                return "black";
        }
    }, [currentStatus]);

    const handlePress = () => {
        setDataQK(
            QK.scheduling.internal.getSchedule(
                appointment.appointment_id.toString(),
            ),
            appointment,
        );
        router.push(
            `/scheduling/internal/${appointment.appointment_id}`,
        );
    };

    const Time = useCallback(() => {
        if (appointment.is_flexible_time) {
            return (
                <XStack
                    items="center"
                    gap="$2"
                    mt="$1"
                >
                    <Clock size={16} />
                    <Text
                        fontSize="$4"
                        color={textColor}
                        ml="$1"
                    >
                        Flexible Time
                    </Text>
                </XStack>
            );
        }

        if (
            appointment.start_time &&
            appointment.end_time
        ) {
            return (
                <XStack
                    items="center"
                    gap="$1"
                    mt="$1"
                >
                    <Clock size={16} />
                    <Text
                        fontSize="$4"
                        color={textColor}
                        ml="$1"
                    >
                        {new Date(
                            appointment.start_time,
                        ).toLocaleTimeString([], {
                            hour: "2-digit",
                            minute: "2-digit",
                        })}{" "}
                        -{" "}
                        {new Date(
                            appointment.end_time,
                        ).toLocaleTimeString([], {
                            hour: "2-digit",
                            minute: "2-digit",
                        })}
                    </Text>
                </XStack>
            );
        }

        return null;
    }, [
        appointment.is_flexible_time,
        appointment.start_time,
        appointment.end_time,
        textColor,
    ]);

    return (
        <Card
            theme="light"
            marginBottom="$2"
            backgroundColor={bgColor}
            borderRadius={8}
            borderColor="#E9ECEF"
            borderWidth={1}
        >
            <Card.Header>
                <XStack justify="space-between">
                    <YStack gap="$2" flex={1}>
                        <XStack>
                            <Text
                                fontSize="$5"
                                fontWeight="bold"
                                mb="$2"
                                color={textColor}
                            >
                                {
                                    appointment.title
                                }
                            </Text>
                        </XStack>

                        <YStack gap="$1">
                            <XStack
                                items="center"
                                gap="$2"
                            >
                                <Calendar
                                    size={16}
                                />
                                <Text
                                    fontSize="$4"
                                    color={
                                        textColor
                                    }
                                >
                                    {formatDate(
                                        appointment.date,
                                        "MMM dd, yyyy",
                                    )}
                                </Text>
                            </XStack>

                            <XStack
                                items="center"
                                gap="$2"
                            >
                                <BookUser
                                    size={16}
                                />
                                <Text
                                    fontSize="$4"
                                    color={
                                        textColor
                                    }
                                >
                                    {
                                        appointment
                                            .appointment_type
                                            .description
                                    }
                                </Text>
                            </XStack>
                            <XStack
                                items="center"
                                gap="$2"
                            >
                                <MapPin
                                    size={16}
                                />
                                <Text
                                    fontSize="$4"
                                    color={
                                        textColor
                                    }
                                    numberOfLines={
                                        1
                                    }
                                >
                                    {
                                        appointment.meeting_location
                                    }
                                </Text>
                            </XStack>
                        </YStack>

                        <Time />
                    </YStack>

                    <YStack>
                        <Button
                            size="$2"
                            bg="transparent"
                            borderColor="$accent1"
                            onPress={handlePress}
                        >
                            <Eye size={16} />
                        </Button>
                    </YStack>
                </XStack>
            </Card.Header>
        </Card>
    );
};

export default InternalScheduleList;
