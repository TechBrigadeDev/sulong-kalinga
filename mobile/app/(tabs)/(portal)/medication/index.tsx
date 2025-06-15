import LoadingScreen from "components/loaders/LoadingScreen";
import { Stack } from "expo-router";
import { useGetMedications } from "features/portal/medication/hook";
import PortalMedicationList from "features/portal/medication/list";
import PortalMedicationSearch from "features/portal/medication/list/_components/Search";
import { YStack } from "tamagui";

const Screen = () => {
    const { isLoading } = useGetMedications();

    const List = () =>
        isLoading ? (
            <LoadingScreen />
        ) : (
            <PortalMedicationList />
        );

    return (
        <YStack flex={1} bg="$background">
            <YStack gap="$3" p="$4" pb="$2">
                <PortalMedicationSearch />
            </YStack>
            <YStack flex={1}>
                <List />
            </YStack>
        </YStack>
    );
};

const Layout = () => {
    return (
        <>
            <Stack.Screen
                options={{
                    title: "Medication",
                    headerShown: true,
                    headerTitleAlign: "center",
                }}
            />
            <Screen />
        </>
    );
};

export default Layout;
