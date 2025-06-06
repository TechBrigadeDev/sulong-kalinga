import { WeekCalendar } from "components/calendars";
import WeekCalendarButton from "components/calendars/WeekCalendar/button";
import { Stack } from "expo-router";
import { useMedicationSchedule } from "features/scheduling/medication/medication.hook";
import { YStack } from "tamagui";

const Screen = () => {
    useMedicationSchedule();
    return (
        <YStack flex={1} bg="$background">
            <YStack>
                <WeekCalendar />
                <WeekCalendarButton />
            </YStack>
            <YStack flex={1} bg="red"></YStack>
        </YStack>
    );
};

const Layout = () => (
    <>
        <Stack.Screen
            options={{
                title: "Medication Schedule",
                headerShown: true,
            }}
        />
        <Screen />
    </>
);

export default Layout;
