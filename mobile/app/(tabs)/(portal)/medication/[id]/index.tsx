import LoadingScreen from "components/loaders/LoadingScreen";
import {
    Redirect,
    Stack,
    useLocalSearchParams,
} from "expo-router";
import PortalMedicationDetail from "features/portal/medication/detail";
import { useGetMedications } from "features/portal/medication/hook";
import { SafeAreaView } from "react-native";

const Screen = () => {
    const { id } = useLocalSearchParams<{
        id: string;
    }>();
    const { data, isLoading, isError } =
        useGetMedications();

    if (isLoading) {
        return <LoadingScreen />;
    }

    if (isError || !data) {
        return (
            <Redirect href="/(tabs)/(portal)/medication" />
        );
    }

    const medication = data.find(
        (med) =>
            med.medication_schedule_id.toString() ===
            id,
    );

    if (!medication) {
        return (
            <Redirect href="/(tabs)/(portal)/medication" />
        );
    }

    return (
        <SafeAreaView
            style={{
                flex: 1,
                backgroundColor: "#fff",
            }}
        >
            <PortalMedicationDetail
                medication={medication}
            />
        </SafeAreaView>
    );
};

const Layout = () => (
    <>
        <Stack.Screen
            options={{
                title: "Medication Details",
                headerShown: true,
            }}
        />
        <Screen />
    </>
);

export default Layout;
