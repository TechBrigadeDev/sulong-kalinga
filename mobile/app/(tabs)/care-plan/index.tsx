import { Stack } from "expo-router";
import WCPForm from "features/care-plan/form";
import { SafeAreaView } from "react-native";

const Screen = () => {
    return (
        <SafeAreaView style={{ flex: 1, padding: 16 }}>
            <Stack.Screen
                options={{
                    headerShown: false,
                }}
            />
            <WCPForm/>
        </SafeAreaView>
    )
}

export default Screen;