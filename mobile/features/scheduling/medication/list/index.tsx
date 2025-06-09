import { formatDate } from "common/date";
import { QK, setDataQK } from "common/query";
import FlatList from "components/FlatList";
import { useRouter } from "expo-router";
import { useMedicationSchedules } from "features/scheduling/medication/medication.hook";
import {
    IGroupedMedicationSchedule,
    IMedicationSchedule,
} from "features/scheduling/medication/medication.type";
import { groupMedicationScheduleByBeneficiary } from "features/scheduling/medication/medication.util";
import {
    Calendar,
    Eye,
} from "lucide-react-native";
import { useMemo } from "react";
import { RefreshControl } from "react-native";
import {
    Button,
    Card,
    Spinner,
    Text,
    View,
    YStack,
} from "tamagui";

import { medicationScheduleListStore } from "./store";

const MedicationList = () => {
    const { search } =
        medicationScheduleListStore();
    const {
        data,
        isLoading,
        isFetchingNextPage,
        hasNextPage,
        fetchNextPage,
        refetch,
    } = useMedicationSchedules({
        search,
    });

    const onLoadMore = () => {
        if (hasNextPage && !isFetchingNextPage) {
            fetchNextPage();
        }
    };

    const schedules: IMedicationSchedule[] =
        useMemo(
            () =>
                data?.pages.flatMap(
                    (page) => page.data,
                ) || [],
            [data],
        );

    const groupedSchedules: IGroupedMedicationSchedule[] =
        useMemo(
            () =>
                groupMedicationScheduleByBeneficiary(
                    schedules,
                ),
            [schedules],
        );
    return (
        <FlatList
            data={groupedSchedules}
            renderItem={({ item }) => (
                <ScheduleCard item={item} />
            )}
            onEndReached={onLoadMore}
            onEndReachedThreshold={0.5}
            estimatedItemSize={100}
            refreshControl={
                <RefreshControl
                    refreshing={isLoading}
                    onRefresh={refetch}
                />
            }
            ListFooterComponent={
                isFetchingNextPage ? (
                    <YStack
                        style={{
                            padding: 16,
                            alignItems: "center",
                        }}
                    >
                        <Spinner />
                    </YStack>
                ) : null
            }
        />
    );
};

const ScheduleCard = ({
    item,
}: {
    item: IGroupedMedicationSchedule;
}) => {
    const fullName = `${item.beneficiary.first_name} ${item.beneficiary.last_name}`;
    return (
        <Card
            theme="light"
            marginBottom="$2"
            padding="$3"
            bg="#F8F9FA"
            borderRadius={8}
            borderColor="#E9ECEF"
            borderWidth={1}
        >
            <YStack>
                <Text
                    fontSize="$7"
                    fontWeight="bold"
                    mb="$2"
                >
                    {fullName}
                </Text>
                <YStack gap="$2">
                    {item.medication_schedules.map(
                        (schedule, idx) => (
                            <Schedule
                                key={`schedule-${idx}-${schedule.medication_schedule_id}`}
                                schedule={
                                    schedule
                                }
                            />
                        ),
                    )}
                </YStack>
            </YStack>
        </Card>
    );
};

const Schedule = ({
    schedule,
}: {
    schedule: IMedicationSchedule;
}) => {
    const router = useRouter();

    const handlePress = () => {
        setDataQK(
            QK.scheduling.medication.getSchedule(
                schedule.medication_schedule_id.toString(),
            ),
            schedule,
        );
        router.push(
            `/scheduling/medication/${schedule.medication_schedule_id}`,
        );
    };

    const startDate = formatDate(
        schedule.start_date,
        "MMM dd, yyyy",
    );
    const endDate = useMemo(() => {
        if (schedule.end_date) {
            return formatDate(
                schedule.end_date,
                "MMM dd, yyyy",
            );
        }
        return "";
    }, [schedule.end_date]);

    return (
        <Card
            key={schedule.medication_schedule_id}
            theme="light"
            marginBottom="$2"
            padding="$3"
            bg="#FFFFFF"
            borderRadius={8}
            borderColor="#E9ECEF"
            borderWidth={1}
            display="flex"
            flexDirection="row"
            justifyContent="space-between"
        >
            <YStack marginBlockEnd="$2">
                <Text
                    fontSize="$5"
                    fontWeight="bold"
                >
                    {schedule.medication_name}
                </Text>
                <View
                    display="flex"
                    flexDirection="row"
                    items="center"
                    gap="$2"
                >
                    <Calendar size={16} />
                    <Text fontSize="$4">
                        {startDate}
                        {endDate
                            ? ` - ${endDate}`
                            : ""}
                    </Text>
                </View>
            </YStack>
            <YStack>
                <Button
                    size="$2"
                    onPress={handlePress}
                >
                    <Eye size={16} />
                </Button>
            </YStack>
        </Card>
    );
};

export default MedicationList;
