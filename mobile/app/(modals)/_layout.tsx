import { Stack } from "expo-router";

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
