import { Stack } from "expo-router";
import { SafeAreaView } from "react-native-safe-area-context";
import { Text } from "tamagui";

const Screen = () => {
    return (
        <SafeAreaView style={{ flex: 1 }}>
            <Text>Emergency Service</Text>
        </SafeAreaView>
    );
};

const Layout = () => {
    return (
        <>
            <Stack.Screen
                name="(portal)/emergency-service"
                options={{
                    headerTitle:
                        "Emergency & Service Request",
                    headerShown: true,
                }}
            />
            <Screen />
        </>
    );
};

export default Layout;
