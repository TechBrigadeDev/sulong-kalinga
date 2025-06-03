import { Stack } from "expo-router";
import WCPForm from "features/care-plan/form";
import { SafeAreaProvider, useSafeAreaInsets } from "react-native-safe-area-context";

const Screen = () => {
    const insets = useSafeAreaInsets();
    return (
        <SafeAreaProvider style={{ flex: 1, marginBottom: insets.bottom }}>
            <Stack.Screen
                options={{
                    headerTitle: "Weekly Care Plan",
                }}
            />
            <WCPForm/>
        </SafeAreaProvider>

    )
}

export default Screen;