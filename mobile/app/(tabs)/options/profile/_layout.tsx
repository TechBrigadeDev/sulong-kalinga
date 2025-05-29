import { Slot, Stack } from "expo-router";
import React from "react";

const Layout = () => {
    return (
        <Stack
            screenOptions={{ headerShown: false }}
        >
            <Slot />
        </Stack>
    );
}

export default Layout;