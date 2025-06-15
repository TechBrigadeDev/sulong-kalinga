import { Stack } from "expo-router";
import FamilyList from "features/portal/family/list";
import { SafeAreaView } from "react-native-safe-area-context";

const Screen = () => {
    return (
        <SafeAreaView style={{ flex: 1 }}>
            <FamilyList />
        </SafeAreaView>
    );
};

const Layout = () => {
    return (
        <>
            <Stack.Screen
                options={{
                    title: "Family Members",
                    headerShown: true,
                    headerTitleAlign: "center",
                }}
            />
            <Screen />
        </>
    );
};

export default Layout;
