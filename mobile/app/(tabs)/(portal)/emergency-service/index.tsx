import TabScroll from "components/tabs/TabScroll";
import { Stack } from "expo-router";
import EmergencyServiceFormSelector from "features/emergency-service/emergency/_components/form-selector";
import { SafeAreaView } from "react-native-safe-area-context";

const Screen = () => {
    return (
        <SafeAreaView style={{ flex: 1 }}>
            <TabScroll
                flex={1}
                display="flex"
                flexDirection="column"
                tabbed
                showScrollUp
            >
                <EmergencyServiceFormSelector />
            </TabScroll>
        </SafeAreaView>
    );
};

const Layout = () => {
    return (
        <>
            <Stack.Screen
                options={{
                    headerTitle:
                        "Emergency & Service Request",
                    headerShown: true,
                    headerBackVisible: true,
                }}
            />
            <Screen />
        </>
    );
};

export default Layout;
