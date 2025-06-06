import { Stack, useRouter } from "expo-router";
import { StyleSheet } from "react-native";
import { Button, View, YStack } from "tamagui";

import FamilyList from "~/features/user-management/components/family/list";
import FamilySearch from "~/features/user-management/components/family/list/search";

const Family = () => {
    const router = useRouter();

    const handleAddFamilyMember = () => {
        router.push("/(tabs)/options/user-management/family/add");
    };

    return (
        <View flex={1} bg="#FFCC80">
            <Stack.Screen
                options={{
                    title: "Family Members",
                }}
            />
            <View style={style.container}>
                <YStack py="$4" gap="$4">
                    <Button size="$3" theme="dark_blue" onPressIn={handleAddFamilyMember}>
                        Add Family Member
                    </Button>
                    <FamilySearch />
                </YStack>
                <View style={{ flex: 1 }}>
                    <FamilyList />
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

export default Family;
