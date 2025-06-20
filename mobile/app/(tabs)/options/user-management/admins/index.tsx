import { Stack } from "expo-router";
import { StyleSheet } from "react-native";
import { SafeAreaView } from "react-native-safe-area-context";
import { View, YStack } from "tamagui";

import AdminList from "~/features/user-management/components/administrators/list";
import AdminSearch from "~/features/user-management/components/administrators/list/search";

const Administrators = () => {
    // const router = useRouter();
    // const handleAddAdmin = () => {
    //     router.push(
    //         "/(tabs)/options/user-management/admins/add",
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
                    title: "Administrators",
                }}
            />
            <View style={style.container}>
                <YStack gap="$4">
                    {/* <Button
                        size="$3"
                        theme="dark_blue"
                        onPressIn={handleAddAdmin}
                    >
                        Add Administrator
                    </Button> */}
                    <AdminSearch />
                </YStack>
                <View style={{ flex: 1 }}>
                    <AdminList />
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

export default Administrators;
