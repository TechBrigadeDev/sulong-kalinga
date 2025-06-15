import {
    formatTime,
    isSameDay,
} from "common/date";
import { QK, setDataQK } from "common/query";
import { weekCalendarStore } from "components/calendars/WeekCalendar/store";
import FlatList from "components/FlatList";
import { useRouter } from "expo-router";
import { useVisitations } from "features/scheduling/visitation/hook";
import { IVisitation } from "features/scheduling/visitation/type";
import { visitTypeLabel } from "features/scheduling/visitation/util";
import {
    BookUser,
    Clock,
    Eye,
} from "lucide-react-native";
import { useCallback, useMemo } from "react";
import {
    Button,
    Card,
    GetThemeValueForKey,
    Text,
    XStack,
    YStack,
} from "tamagui";

const VisitationList = () => {
    const { data, isLoading } = useVisitations();

    if (!data || isLoading) {
        return null;
    }

    return (
        <FlatList<IVisitation>
            data={data}
            renderItem={({ item }) => (
                <Schedule visitation={item} />
            )}
        />
    );
};

const Schedule = ({
    visitation,
}: {
    visitation: IVisitation;
}) => {
    const router = useRouter();
    const { date } = weekCalendarStore();
    const beneficiary = `${visitation.beneficiary.first_name} ${visitation.beneficiary.last_name}`;
    const careWorker = `${visitation.care_worker.first_name} ${visitation.care_worker.last_name}`;

    const currentStatus = useMemo(() => {
        if (
            visitation.occurrences.length === 0 ||
            !date
        ) {
            return null;
        }
        const occurrence =
            visitation.occurrences.find(
                (occurrence) =>
                    isSameDay(
                        occurrence.occurrence_date,
                        date,
                    ),
            ) || visitation.occurrences[0];
        if (!occurrence) {
            return null;
        }
        return occurrence.status;
    }, [visitation.occurrences, date]);

    const bg = useMemo<
        GetThemeValueForKey<"backgroundColor">
    >(() => {
        if (!currentStatus) {
            return "$yellow1";
        }

        switch (currentStatus) {
            case "completed":
                return "$green5";
            case "canceled":
                return "$red10";
            case "scheduled":
                return "$blue8";
            default:
                return "$yellow1";
        }
    }, [currentStatus]);

    const color = useMemo<
        GetThemeValueForKey<"color">
    >(() => {
        if (!currentStatus) {
            return "$yellow11";
        }

        switch (currentStatus) {
            case "completed":
                return "black";
            case "scheduled":
            case "canceled":
                return "white";
            default:
                return "$yellow11";
        }
    }, [currentStatus]);

    const handlePress = () => {
        setDataQK(
            QK.scheduling.visitation.getVisitation(
                visitation.visitation_id.toString(),
            ),
            visitation,
        );
        router.push(
            `/scheduling/visitations/${visitation.visitation_id}`,
        );
    };

    const Time = useCallback(() => {
        if (visitation.is_flexible_time) {
            return (
                <XStack
                    items="center"
                    gap="$1"
                    mt="$1"
                >
                    <Clock size={16} />
                    <Text
                        fontSize="$4"
                        color={color}
                        ml="$1"
                    >
                        Flexible Time
                    </Text>
                </XStack>
            );
        }
        return (
            <Text>
                {formatTime(
                    visitation.start_time ?? "",
                )}{" "}
                -{" "}
                {formatTime(
                    visitation.end_time ?? "",
                )}
            </Text>
        );
    }, [
        visitation.is_flexible_time,
        visitation.start_time,
        visitation.end_time,
        color,
    ]);

    return (
        <Card
            theme="light"
            marginBottom="$2"
            bg={bg}
            borderRadius={8}
            borderColor="#E9ECEF"
            borderWidth={1}
        >
            <Card.Header>
                <XStack
                    display="flex"
                    justify="space-between"
                >
                    <YStack gap="$2">
                        <XStack>
                            <Text
                                fontSize="$5"
                                fontWeight="bold"
                                mb="$2"
                                color={color}
                            >
                                {beneficiary}
                            </Text>
                        </XStack>
                        <YStack gap="$1">
                            <XStack
                                items="center"
                                gap="$1"
                            >
                                <BookUser
                                    size={16}
                                />
                                <Text
                                    fontSize="$4"
                                    color={color}
                                >
                                    {careWorker}
                                </Text>
                            </XStack>
                            <Text
                                fontSize="$4"
                                color={color}
                            >
                                {visitTypeLabel(
                                    visitation.visit_type,
                                )}
                            </Text>
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

export default VisitationList;
