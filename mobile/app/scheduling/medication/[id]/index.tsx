import LoadingScreen from "components/loaders/LoadingScreen";
import {
    Redirect,
    Stack,
    useLocalSearchParams,
} from "expo-router";
import MedicationScheduleDetail from "features/scheduling/medication/detail";
import { useMedicationSchedule } from "features/scheduling/medication/medication.hook";
import { SafeAreaView } from "react-native";

const Screen = () => {
    const { id } = useLocalSearchParams<{
        id: string;
    }>();

    const { data, isLoading, isError, error } =
        useMedicationSchedule(id);

    if (isLoading) {
        return <LoadingScreen />;
    }

    if (!data || isError) {
        if (error instanceof Error) {
            console.error(error.message);
        }
        return (
            <Redirect href="/scheduling/medication" />
        );
    }

    return (
        <SafeAreaView
            style={{
                flex: 1,
                backgroundColor: "#fff",
            }}
        >
            <MedicationScheduleDetail
                schedule={data}
            />
        </SafeAreaView>
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
