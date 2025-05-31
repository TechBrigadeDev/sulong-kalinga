import { Stack } from "expo-router";
import { Text, View } from "tamagui"
import { useGetCareManagers } from "../../../../../features/user/management/management.hook";

const CareManagers = () => {
    useGetCareManagers();
    return (
        <View>
            <Stack.Screen
                options={{
                    title: "Care Managers",
                }}
            />
            <Text>Care Managers</Text>
        </View>
    )
}

export default CareManagers;