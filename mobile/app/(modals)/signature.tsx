import DrawingCanvas from "components/drawing";
import { Stack } from "expo-router";
import { StyleSheet } from "react-native";
import {
    SafeAreaView,
    useSafeAreaInsets,
} from "react-native-safe-area-context";

const Screen = () => {
    const { bottom } = useSafeAreaInsets();
    return (
        <SafeAreaView
            style={[
                style.container,
                { paddingRight: bottom },
            ]}
        >
            <DrawingCanvas />
        </SafeAreaView>
    );
};

const style = StyleSheet.create({
    container: {
        flex: 1,
        backgroundColor: "white",
    },
});

const Layout = () => {
    return (
        <>
            <Stack.Screen
                name="signature"
                options={{
                    title: "Signature",
                    presentation: "modal",
                    headerShown: false,
                    orientation: "landscape",
                }}
            />
            <Screen />
        </>
    );
};

export default Layout;
