import { Stack } from "expo-router";
import { useGetVisitations } from "features/portal/visitation/hook";
import VisitationList from "features/portal/visitation/list";
import { SafeAreaView } from "react-native-safe-area-context";

const Screen = () => {
    useGetVisitations();
    return (
        <SafeAreaView style={{ flex: 1 }}>
            <VisitationList />
        </SafeAreaView>
    );
};

const Layout = () => {
    return (
        <>
            <Stack.Screen
                options={{
                    title: "Visitations",
                    headerShown: true,
                }}
            />
            <Screen />
        </>
    );
};

export default Layout;
