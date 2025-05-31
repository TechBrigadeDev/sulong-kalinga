import { Stack, useLocalSearchParams } from "expo-router";
import CareManagerDetail from "features/user/management/components/care-managers/detail";
import { Text, View } from "tamagui";

import { useGetCareManager } from "~/features/user/management/management.hook";

const Screen = () => {
    const { id } = useLocalSearchParams();

    const {
     data,
     isLoading
    } = useGetCareManager(id as string);

    if (isLoading) {
        return (
            <View>
                <Text>Loading...</Text>
            </View>
        )
    }

    if (!data) {
        return (
            <View padding="$4" justifyContent="center" alignItems="center">
                <Text>No Care Manager found</Text>
            </View>
        )
    }

    return (
        <>
            <Stack.Screen options={{
                title: "Care Manager Details",
                headerShown: true
            }}/>
            <CareManagerDetail careManager={data} />
        </>
    )
}

export default Screen;