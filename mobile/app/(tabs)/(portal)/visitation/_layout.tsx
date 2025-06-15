import { Stack } from "expo-router";

const Layout = () => {
    return (
        <Stack
            screenOptions={{
                headerShown: false,
                headerTitleAlign: "center",
            }}
        />
    );
};

export default Layout;
