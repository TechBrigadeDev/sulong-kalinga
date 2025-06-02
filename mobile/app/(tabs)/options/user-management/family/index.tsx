import { Stack, useRouter } from "expo-router";
import { StyleSheet } from "react-native";
import { Button, Card, View } from "tamagui"

import FamilyList from "~/features/user/management/components/family/list";
import FamilySearch from "~/features/user/management/components/family/list/search";

const Family = () => {
    const router = useRouter();

    const handleAddFamilyMember = () => {
        router.push("/(tabs)/options/user-management/family/add");
    };

    return (
        <View flex={1} bg="$background">
            <Stack.Screen
                options={{
                    title: "Family Members",
                }}
            />
            <View style={style.container}>
                <Card
                    paddingVertical={20}
                    marginVertical={20}
                    borderRadius={10}
                    display="flex"
                    gap="$4"
                >
                    <Button
                        size="$3"
                        theme="dark_blue"
                        onPressIn={handleAddFamilyMember}
                    >
                        Add Family Member
                    </Button>
                    <FamilySearch/>
                </Card>
                <FamilyList />
            </View>
        </View>
    )
}

const style = StyleSheet.create({
    container: {
        flex: 1,
        marginHorizontal: 30
    }
})

export default Family;