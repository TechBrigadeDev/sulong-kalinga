import { Stack } from "expo-router";
import { StyleSheet } from "react-native";
import { Card, View } from "tamagui";

import AdminList from "~/features/user/management/components/administrators/list";
import AdminSearch from "~/features/user/management/components/administrators/list/search";

const Administrators = () => {
    return (
        <View flex={1} bg="$background">
            <Stack.Screen
                options={{
                    title: "Administrators",
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
                    <AdminSearch />
                </Card>
                <AdminList />
            </View>
        </View>
    )
}

const style = StyleSheet.create({
    container: {
        marginHorizontal: 30,
        flex: 1,
    },
});

export default Administrators;