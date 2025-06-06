import LoadingScreen from "components/loaders/LoadingScreen";
import { Stack } from "expo-router";
import MedicationList from "features/scheduling/medication/list";
import MedicationScheduleSearch from "features/scheduling/medication/list/_components/Search";
import { medicationScheduleListStore } from "features/scheduling/medication/list/store";
import { useMedicationSchedules } from "features/scheduling/medication/medication.hook";
import { YStack } from "tamagui";

const Screen = () => {
    const { search } =
        medicationScheduleListStore();
    const { isLoading } = useMedicationSchedules({
        search,
    });

    const List = () =>
        isLoading ? (
            <LoadingScreen />
        ) : (
            <MedicationList />
        );

    return (
        <YStack flex={1} bg="$background">
            <YStack>
                <MedicationScheduleSearch
                    mt="$4"
                    mx="$4"
                />
            </YStack>
            <YStack flex={1} marginInline="$4">
                <List />
            </YStack>
        </YStack>
    );
};

const Layout = () => (
    <>
        <Stack.Screen
            options={{
                title: "Medication",
                headerShown: true,
            }}
        />
        <Screen />
    </>
);

export default Layout;
