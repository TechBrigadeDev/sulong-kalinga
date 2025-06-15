import { Stack } from "expo-router";
import { SafeAreaView } from "react-native-safe-area-context";
import { Text } from "tamagui";

const Screen = () => {
    return (
        <SafeAreaView style={{ flex: 1 }}>
            <Text>Visitation</Text>
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
