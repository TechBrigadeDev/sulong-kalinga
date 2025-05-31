import { Stack, useLocalSearchParams } from "expo-router";
import { Text, View } from "tamagui";
import { useGetCareWorker } from "~/features/user/management/management.hook";


const Screen = () => {
    const { id } = useLocalSearchParams();

    const {
     data,
     isLoading
    } = useGetCareWorker(id as string);

    if (isLoading) {
        return (
            <View>
                <Text>Loading...</Text>
            </View>
        )
    }

    if (!data) {
        return (
            <View>
                <Text>No beneficiary found</Text>
            </View>
        )
    }

    return (
        <>
            <Stack.Screen/>
        </>
    )
}

export default Screen;