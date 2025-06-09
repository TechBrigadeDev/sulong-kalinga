import { Stack } from "expo-router";
import { SafeAreaView } from "react-native";

import UpdatePassword from "~/features/user/components/UpdatePassword";

const Screen = () => {
    return (
        <SafeAreaView style={style.container}>
            <Stack.Screen
                options={{
                    headerTitle:
                        "Update Password",
                    headerShown: true,
                }}
            />
            <UpdatePassword />
        </SafeAreaView>
    );
};

const style = {
    container: {
        flex: 1,
    },
};

export default Screen;
