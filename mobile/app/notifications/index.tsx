import { Stack } from "expo-router";
import ReadAllNotification from "features/notification/_components/ReadAll";
import NotificationList from "features/notification/list";
import { SafeAreaView } from "react-native";
import { XStack, YStack } from "tamagui";

const Screen = () => {
    return (
        <SafeAreaView style={{ flex: 1 }}>
            <YStack flex={1} bg="$background">
                {/* <YStack gap="$3" p="$4" pb="$2">
                    <NotificationSearch />
                </YStack> */}
                <YStack flex={1}>
                    <NotificationList />
                </YStack>
            </YStack>
        </SafeAreaView>
    );
};

const headerRight = () => {
    return (
        <XStack gap="$2">
            <ReadAllNotification />
        </XStack>
    );
};

const Layout = () => (
    <>
        <Stack.Screen
            name="notification"
            options={{
                title: "Notifications",
                headerTitle: "Notifications",
                headerRight,
            }}
        />
        <Screen />
    </>
);

export default Layout;
