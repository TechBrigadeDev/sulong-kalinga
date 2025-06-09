import { Stack } from "expo-router";
import { SafeAreaView } from "react-native-safe-area-context";
import { StyleSheet } from "react-native";
import { View, YStack } from "tamagui";

import ReportsList from "~/features/reports/list";
import ReportsSearch from "~/features/reports/list/search";

const Screen = () => {
    return (
        <SafeAreaView style={{ flex: 1 }}>
            <View style={styles.container}>
                <YStack py="$4" gap="$4">
                    <ReportsSearch />
                </YStack>
                <View style={{ flex: 1 }}>
                    <ReportsList />
                </View>
            </View>
        </SafeAreaView>
    );
};

const styles = StyleSheet.create({
    container: {
        flex: 1,
        paddingHorizontal: 16,
    },
});

const Layout = () => (
    <>
        <Stack.Screen
            options={{
                title: "Care Records",
                headerShown: true,
            }}
        />
        <Screen />
    </>
);

export default Layout;
