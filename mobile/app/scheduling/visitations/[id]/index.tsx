import LoadingScreen from "components/loaders/LoadingScreen";
import { Redirect, Stack } from "expo-router";
import { useLocalSearchParams } from "expo-router/build/hooks";
import VisitationDetail from "features/scheduling/visitation/detail";
import { useVisitation } from "features/scheduling/visitation/hook";
import { SafeAreaView } from "react-native-safe-area-context";

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
        <SafeAreaView
            style={{
                flex: 1,
                backgroundColor: "#fff",
            }}
        >
            <VisitationDetail
                visitation={data}
                flex={1}
            />
        </SafeAreaView>
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
