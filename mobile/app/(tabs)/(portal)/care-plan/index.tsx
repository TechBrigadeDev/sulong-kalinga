import { Stack } from "expo-router";
import CarePlanList from "features/portal/care-plan/list";
import { SafeAreaView } from "react-native-safe-area-context";

const Screen = () => {
    return (
        <SafeAreaView style={{ flex: 1 }}>
            <CarePlanList />
        </SafeAreaView>
    );
};

const Layout = () => {
    return (
        <>
            <Stack.Screen
                options={{
                    title: "Care Plan",
                    headerShown: true,
                    headerTitleAlign: "center",
                }}
            />
            <Screen />
        </>
    );
};

export default Layout;
