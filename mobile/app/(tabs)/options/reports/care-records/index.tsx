import { Stack, useRouter } from "expo-router";
import { Plus } from "lucide-react-native";
import { StyleSheet } from "react-native";
import { SafeAreaView } from "react-native-safe-area-context";
import { Button, View, YStack } from "tamagui";

import WCPRecordsList from "~/features/records/wcp/list";
import WCPRecordsSearch from "~/features/records/wcp/list/search";

const Screen = () => {
    const router = useRouter();

    const handleCreateRecord = () => {
        router.push("/care-plan");
    };

    return (
        <SafeAreaView style={{ flex: 1 }}>
            <View style={styles.container}>
                <YStack py="$4" gap="$4">
                    <WCPRecordsSearch />
                    <Button
                        size="$3"
                        theme="black"
                        onPress={
                            handleCreateRecord
                        }
                    >
                        <Plus
                            size={16}
                            color={"white"}
                        />
                        Create
                    </Button>
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
                title: "Weekly Care Plan",
                headerShown: true,
            }}
        />
        <Screen />
    </>
);

export default Layout;
