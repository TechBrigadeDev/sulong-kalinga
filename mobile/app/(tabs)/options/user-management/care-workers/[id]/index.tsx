import { Stack, useLocalSearchParams } from "expo-router";
import { View } from "react-native";
import { Text } from "tamagui";
import CareWorkerDetail from "features/user/management/components/care-workers/detail";
import { useGetCareWorker } from "~/features/user/management/management.hook";


const Screen = () => {
    const { id } = useLocalSearchParams();

    const {
     data,
     isLoading
    } = useGetCareWorker(id as string);

    if (isLoading) {
        return (
            <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center' }}>
                <Text>Loading...</Text>
            </View>
        );
    }

    if (!data) {
        return (
            <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center' }}>
                <Text>No Care Worker found</Text>
            </View>
        );
    }

    return (
        <>
            <Stack.Screen options={{
                title: "Care Worker Details",
                headerShown: true
            }}/>
            <CareWorkerDetail careWorker={data} />
        </>
    );
}

export default Screen;