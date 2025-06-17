import { Stack, useRouter } from "expo-router";
import { StyleSheet } from "react-native";
import { SafeAreaView } from "react-native-safe-area-context";
import { Button, View, YStack } from "tamagui";

import FamilyList from "~/features/user-management/components/family/list";
import FamilySearch from "~/features/user-management/components/family/list/search";

const Family = () => {
    // const router = useRouter();
    // const handleAddFamilyMember = () => {
    //     router.push(
    //         "/(tabs)/options/user-management/family/add",
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
                    title: "Family Members",
                }}
            />
            <View style={style.container}>
                <YStack gap="$4">
                    {/* <Button
                        size="$3"
                        theme="dark_blue"
                        onPressIn={
                            handleAddFamilyMember
                        }
                    >
                        Add Family Member
                    </Button> */}
                    <FamilySearch />
                </YStack>
                <View style={{ flex: 1 }}>
                    <FamilyList />
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

export default Family;
