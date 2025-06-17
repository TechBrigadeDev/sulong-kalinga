import { Stack } from "expo-router";
import WCPForm from "features/care-plan/form";
import {
    SafeAreaProvider,
    useSafeAreaInsets,
} from "react-native-safe-area-context";

const Screen = () => {
    const insets = useSafeAreaInsets();
    return (
        <SafeAreaProvider
            style={{
                flex: 1,
                marginBottom: insets.bottom,
            }}
        >
            <WCPForm />
        </SafeAreaProvider>
    );
};

const Layout = () => (
    <>
        <Stack.Screen
            options={{
                title: "Weekly Care Plan",
                headerShown: true,
            }}
        />
        <Screen />
    </>
);

export default Layout;
