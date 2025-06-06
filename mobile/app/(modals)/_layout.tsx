import { Stack } from "expo-router";
import { StyleSheet } from "react-native";

const Layout = () => {
    return (
        <Stack>
            <Stack.Screen
                name="select-beneficiary"
                options={{
                    presentation: "modal",
                }}
            />
        </Stack>
    );
};

export default Layout;
