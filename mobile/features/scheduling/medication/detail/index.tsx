import ScrollView from "components/ScrollView";
import { Stack } from "expo-router";
import { IMedicationSchedule } from "features/scheduling/medication/medication.type";
import { YStack } from "tamagui";

import DurationCard from "./DurationCard";
import HeaderCard from "./HeaderCard";
import ScheduleCard from "./ScheduleCard";
import SpecialInstructionsCard from "./SpecialInstructionsCard";

interface Props {
    schedule?: IMedicationSchedule;
}

const extractScheduleProps = (
    schedule?: IMedicationSchedule,
) => {
    if (!schedule) return {};
    return {
        medication_name: schedule.medication_name,
        dosage: schedule.dosage,
        medication_type: schedule.medication_type,
        status: schedule.status,
        morning_time:
            schedule.morning_time || undefined,
        noon_time:
            schedule.noon_time || undefined,
        evening_time:
            schedule.evening_time || undefined,
        night_time:
            schedule.night_time || undefined,
        with_food_morning:
            schedule.with_food_morning,
        with_food_noon: schedule.with_food_noon,
        with_food_evening:
            schedule.with_food_evening,
        with_food_night: schedule.with_food_night,
        as_needed: schedule.as_needed,
        special_instructions:
            schedule.special_instructions ||
            undefined,
        start_date: schedule.start_date,
        end_date: schedule.end_date || undefined,
        beneficiary: schedule.beneficiary,
    };
};

const MedicationDetail = ({
    schedule,
}: Props) => {
    const scheduleData =
        extractScheduleProps(schedule);

    const fullName = `${scheduleData.beneficiary?.first_name} ${scheduleData.beneficiary?.last_name}`;

    return (
        <>
            <Stack.Screen
                options={{
                    headerTitle: fullName,
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
                    <HeaderCard
                        medication_name={
                            scheduleData.medication_name
                        }
                        dosage={
                            scheduleData.dosage
                        }
                        medication_type={
                            scheduleData.medication_type
                        }
                        status={
                            scheduleData.status
                        }
                    />
                    <ScheduleCard
                        morning_time={
                            scheduleData.morning_time
                        }
                        noon_time={
                            scheduleData.noon_time
                        }
                        evening_time={
                            scheduleData.evening_time
                        }
                        night_time={
                            scheduleData.night_time
                        }
                        with_food_morning={
                            scheduleData.with_food_morning
                        }
                        with_food_noon={
                            scheduleData.with_food_noon
                        }
                        with_food_evening={
                            scheduleData.with_food_evening
                        }
                        with_food_night={
                            scheduleData.with_food_night
                        }
                        as_needed={
                            scheduleData.as_needed
                        }
                    />
                    <DurationCard
                        start_date={
                            scheduleData.start_date
                        }
                        end_date={
                            scheduleData.end_date
                        }
                    />
                    <SpecialInstructionsCard
                        special_instructions={
                            scheduleData.special_instructions
                        }
                    />
                </YStack>
            </ScrollView>
        </>
    );
};

export default MedicationDetail;
