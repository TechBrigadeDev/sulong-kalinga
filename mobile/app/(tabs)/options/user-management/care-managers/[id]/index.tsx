import {
    Stack,
    useLocalSearchParams,
} from "expo-router";
import { Text, YStack } from "tamagui";

import CareManagerDetail from "~/features/user-management/components/care-managers/detail";
import { useGetCareManager } from "~/features/user-management/management.hook";

const Screen = () => {
    const { id } = useLocalSearchParams();

    const { data, isLoading } = useGetCareManager(
        id as string,
    );

    if (isLoading) {
        return (
            <YStack
                style={{
                    flex: 1,
                    justifyContent: "center",
                    alignItems: "center",
                }}
            >
                <Text>Loading...</Text>
            </YStack>
        );
    }

    if (!data) {
        return (
            <YStack
                style={{
                    flex: 1,
                    justifyContent: "center",
                    alignItems: "center",
                    padding: 16,
                }}
            >
                <Text>No Care Manager found</Text>
            </YStack>
        );
    }

    return (
        <>
            <Stack.Screen
                options={{
                    title: "Care Manager Details",
                    headerShown: true,
                }}
            />
            <CareManagerDetail
                careManager={data}
            />
        </>
    );
};

export default Screen;
