import { Stack } from "expo-router";
import WCPForm from "features/care-plan/form";
import { SafeAreaView } from "react-native";

const Screen = () => {
    return (
        <SafeAreaView style={{ flex: 1 }}>
            <Stack.Screen
                options={{
                    headerTitle: "Weekly Care Plan",
                }}
            />
            <WCPForm/>
        </SafeAreaView>
    )
}

export default Screen;