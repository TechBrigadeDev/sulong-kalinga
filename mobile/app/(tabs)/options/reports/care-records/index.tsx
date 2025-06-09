import { Stack } from "expo-router";
import { SafeAreaView } from "react-native-safe-area-context";

import ReportsList from "~/features/reports/list";

const Screen = () => {
    return (
        <SafeAreaView style={{ flex: 1 }}>
            <ReportsList />
        </SafeAreaView>
    );
};

const Layout = () => (
    <>
        <Stack.Screen
            options={{
                title: "Care Records",
                headerShown: true,
            }}
        />
        <Screen />
    </>
);

export default Layout;
