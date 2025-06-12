import { formatDate } from "common/date";
import { IInternalAppointment } from "features/scheduling/internal/type";
import {
    BookUser,
    Calendar,
    Clock,
    MapPin,
    StickyNote,
    Users,
} from "lucide-react-native";
import {
    Card,
    H6,
    Text,
    XStack,
    YStack,
    YStackProps,
} from "tamagui";

interface Props extends YStackProps {
    appointment: IInternalAppointment;
}

const InternalAppointmentDetail = ({
    appointment,
    ...props
}: Props) => {
    const formatTime = (time?: string) => {
        if (!time) return "";
        return new Date(time).toLocaleTimeString(
            [],
            {
                hour: "2-digit",
                minute: "2-digit",
            },
        );
    };

    return (
        <YStack gap="$4">
            {/* Basic Details Card */}
            <Card elevate bordered p="$4">
                <YStack gap="$4">
                    <H6>Appointment Details</H6>
                    <YStack gap="$2">
                        <XStack
                            gap="$2"
                            items="center"
                        >
                            <BookUser size={16} />
                            <Text>
                                Type:{" "}
                                {
                                    appointment
                                        .appointment_type
                                        .description
                                }
                            </Text>
                        </XStack>

                        <XStack
                            gap="$2"
                            items="center"
                        >
                            <Calendar size={16} />
                            <Text>
                                Date:{" "}
                                {formatDate(
                                    appointment.date,
                                    "MMM dd, yyyy",
                                )}
                            </Text>
                        </XStack>

                        <XStack
                            gap="$2"
                            items="center"
                        >
                            <Clock size={16} />
                            <Text>
                                Time:{" "}
                                {appointment.is_flexible_time
                                    ? "Flexible Time"
                                    : `${formatTime(
                                          appointment.start_time ??
                                              undefined,
                                      )} - ${formatTime(
                                          appointment.end_time ??
                                              undefined,
                                      )}`}
                            </Text>
                        </XStack>

                        <XStack
                            gap="$2"
                            items="center"
                        >
                            <MapPin size={16} />
                            <Text>
                                Location:{" "}
                                {
                                    appointment.meeting_location
                                }
                            </Text>
                        </XStack>

                        <Text>
                            Status:{" "}
                            {appointment.status
                                .charAt(0)
                                .toUpperCase() +
                                appointment.status.slice(
                                    1,
                                )}
                        </Text>
                    </YStack>
                </YStack>
            </Card>

            {/* Description Card */}
            <Card elevate bordered p="$4">
                <YStack gap="$4">
                    <H6>Description</H6>
                    <Text>
                        {appointment.description ||
                            "No description provided"}
                    </Text>
                    {appointment.other_type_details && (
                        <YStack gap="$2">
                            <Text fontWeight="bold">
                                Additional
                                Details:
                            </Text>
                            <Text>
                                {
                                    appointment.other_type_details
                                }
                            </Text>
                        </YStack>
                    )}
                </YStack>
            </Card>

            {/* Participants Card */}
            <Card elevate bordered p="$4">
                <YStack gap="$4">
                    <XStack
                        gap="$2"
                        items="center"
                    >
                        <Users size={16} />
                        <H6>Participants</H6>
                    </XStack>
                    {appointment.participants
                        .length > 0 ? (
                        <YStack gap="$2">
                            {appointment.participants.map(
                                (
                                    participant,
                                    index,
                                ) => (
                                    <XStack
                                        key={
                                            index
                                        }
                                        gap="$2"
                                        items="center"
                                    >
                                        <Text fontSize="$4">
                                            •
                                        </Text>
                                        <Text>
                                            {participant.user
                                                ? `${participant.user.first_name} ${participant.user.last_name}`
                                                : participant.participant_type}
                                            {participant.is_organizer &&
                                                " (Organizer)"}
                                        </Text>
                                    </XStack>
                                ),
                            )}
                        </YStack>
                    ) : (
                        <Text>
                            No participants
                            assigned
                        </Text>
                    )}
                </YStack>
            </Card>

            {/* Notes Card */}
            {appointment.notes && (
                <Card elevate bordered p="$4">
                    <YStack gap="$4">
                        <XStack
                            gap="$2"
                            items="center"
                        >
                            <StickyNote
                                size={16}
                            />
                            <H6>Notes</H6>
                        </XStack>
                        <Text>
                            {appointment.notes}
                        </Text>
                    </YStack>
                </Card>
            )}
        </YStack>
    );
};

export default InternalAppointmentDetail;
