import { Stack, useLocalSearchParams } from "expo-router";
import { Text, View } from "tamagui";
import { useGetAdmin } from "~/features/user/management/management.hook";
import AdminDetail from "~/features/user/management/components/administrators/detail";

const Screen = () => {
    const { id } = useLocalSearchParams<{
        id: string;
    }>();

    const { data, isLoading } = useGetAdmin(id);

    if (isLoading) {
        return (
            <View style={{ padding: 16, justifyContent: 'center', alignItems: 'center' }}>
                <Text>Loading...</Text>
            </View>
        );
    }

    if (!data) {
        return (
            <View style={{ padding: 16, justifyContent: 'center', alignItems: 'center' }}>
                <Text>No administrator found</Text>
            </View>
        );
    }

    return (
        <>
            <Stack.Screen options={{
                title: "Administrator Details",
                headerShown: true
            }}/>
            <AdminDetail admin={data} />
        </>
    );
}

export default Screen;