import LoadingScreen from "components/loaders/LoadingScreen";
import ScrollView from "components/ScrollView";
import { Redirect, Stack } from "expo-router";
import { useLocalSearchParams } from "expo-router/build/hooks";
import VisitationDetail from "features/scheduling/visitation/detail";
import { useVisitation } from "features/scheduling/visitation/hook";

const Screen = () => {
    const { id } = useLocalSearchParams<{
        id: string;
    }>();

    const { data, isLoading } = useVisitation(id);

    if (isLoading) {
        return <LoadingScreen />;
    }

    if (!data) {
        return (
            <Redirect href="/scheduling/visitations" />
        );
    }

    return (
        <ScrollView flex={1} px="$4" pt="$4">
            <VisitationDetail
                visitation={data}
                flex={1}
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
