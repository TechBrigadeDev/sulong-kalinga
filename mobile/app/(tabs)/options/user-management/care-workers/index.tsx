import {
    Stack,
    useLocalSearchParams,
} from "expo-router";
import { StyleSheet } from "react-native";
import { SafeAreaView } from "react-native-safe-area-context";
import { View, YStack } from "tamagui";

import CareWorkerList from "~/features/user-management/components/care-workers/list";
import CareWorkerSearch from "~/features/user-management/components/care-workers/list/search";

const CareWorkers = () => {
    const params = useLocalSearchParams<{
        search?: string;
    }>();

    // const router = useRouter();
    // const handleAddCareWorker = () => {
    //     router.push(
    //         `/(tabs)/options/user-management/care-workers/add`,
    //     );
    // };
    return (
        <SafeAreaView
            style={{
                flex: 1,
                backgroundColor: "#BBDEFB",
            }}
        >
            <Stack.Screen
                options={{
                    title: "Care Workers",
                }}
            />
            <View style={style.container}>
                <YStack gap="$4">
                    {/* <Button
                        size="$3"
                        theme="dark_blue"
                        onPressIn={
                            handleAddCareWorker
                        }
                    >
                        Add Care Worker
                    </Button> */}
                    <CareWorkerSearch
                        search={params.search}
                    />
                </YStack>
                <View style={{ flex: 1 }}>
                    <CareWorkerList />
                </View>
            </View>
        </SafeAreaView>
    );
};

const style = StyleSheet.create({
    container: {
        flex: 1,
        paddingHorizontal: 16,
    },
});
export default CareWorkers;
