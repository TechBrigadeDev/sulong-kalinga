import { Slot, Stack } from "expo-router";
import { SafeAreaView } from "react-native";

const Layout = () => {
    return (
        <Stack screenOptions={{
            headerShown: false,
        }}>
            <SafeAreaView style={{ flex: 1 }}>
                <Slot />
            </SafeAreaView>
        </Stack>
    )
}

export default Layout;