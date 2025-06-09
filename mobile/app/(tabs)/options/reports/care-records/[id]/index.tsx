import { Stack, useLocalSearchParams } from "expo-router";
import { SafeAreaView } from "react-native-safe-area-context";
import { Text, View } from "tamagui";

const Screen = () => {
    const { id } = useLocalSearchParams<{ id: string }>();

    return (
        <SafeAreaView style={{ flex: 1 }}>
            <View>
                <Text>Care Record Details</Text>
                <Text>ID: {id}</Text>
            </View>
        </SafeAreaView>
    );
}

const Layout = () => (
    <>
        <Stack.Screen
            options={{
                title: "Care Record",
                headerShown: true,
            }}
        />
        <Screen />
    </>
);

export default Layout;