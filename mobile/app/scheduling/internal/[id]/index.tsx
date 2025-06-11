import LoadingScreen from "components/loaders/LoadingScreen";
import ScrollView from "components/ScrollView";
import { Redirect, Stack } from "expo-router";
import { useLocalSearchParams } from "expo-router/build/hooks";
import InternalAppointmentDetail from "features/scheduling/internal/detail";
import { useInternalSchedule } from "features/scheduling/internal/hook";

const Screen = () => {
    const { id } = useLocalSearchParams<{
        id: string;
    }>();

    const { data, isLoading } =
        useInternalSchedule(id);

    if (isLoading) {
        return <LoadingScreen />;
    }

    if (!data) {
        return (
            <Redirect href="/scheduling/internal" />
        );
    }

    return (
        <ScrollView flex={1} px="$4" pt="$4">
            <InternalAppointmentDetail
                appointment={data}
            />
        </ScrollView>
    );
};

const Layout = () => {
    return (
        <>
            <Stack.Screen
                options={{
                    title: "Appointment Details",
                    headerShown: true,
                }}
            />
            <Screen />
        </>
    );
};

export default Layout;
