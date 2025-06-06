import { Stack, useRouter } from "expo-router";
import { StyleSheet } from "react-native";
import { Button, View, YStack } from "tamagui";

import CareWorkerList from "~/features/user-management/components/care-workers/list";
import CareWorkerSearch from "~/features/user-management/components/care-workers/list/search";

const CareWorkers = () => {
    const router = useRouter();

    const handleAddCareWorker = () => {
        router.push(
            `/(tabs)/options/user-management/care-workers/add`,
        );
    };
    return (
        <View flex={1} bg="#C8E6C9">
            <Stack.Screen
                options={{
                    title: "Care Workers",
                }}
            />
            <View style={style.container}>
                <YStack py="$4" gap="$4">
                    <Button
                        size="$3"
                        theme="dark_blue"
                        onPressIn={
                            handleAddCareWorker
                        }
                    >
                        Add Care Worker
                    </Button>
                    <CareWorkerSearch />
                </YStack>
                <View style={{ flex: 1 }}>
                    <CareWorkerList />
                </View>
            </View>
        </View>
    );
};

const style = StyleSheet.create({
    container: {
        flex: 1,
        paddingHorizontal: 16,
    },
});
export default CareWorkers;
