import { Stack } from "expo-router";
import { StyleSheet } from "react-native";
import { SafeAreaView } from "react-native-safe-area-context";
import { View, YStack } from "tamagui";

import WCPRecordsList from "~/features/records/wcp/list";
import WCPRecordsSearch from "~/features/records/wcp/list/search";

const Screen = () => {
    return (
        <SafeAreaView style={{ flex: 1 }}>
            <View style={styles.container}>
                <YStack py="$4" gap="$4">
                    <WCPRecordsSearch />
                </YStack>
                <View style={{ flex: 1 }}>
                    <WCPRecordsList />
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
                title: "WCP Records",
                headerShown: true,
            }}
        />
        <Screen />
    </>
);

export default Layout;
