import { StyleSheet } from "react-native";

import GradientBackground from "../components/GradientContainer";
import { Stack } from "expo-router";
import Login from "../features/auth/components/login";

const Screen = () => {
    return (
        <>
            <Stack.Screen
            options={{
                headerShown: false,
            }}
            />
            <GradientBackground
                colors={["#ff6ec7", "#ffa100", "#00d2ff"]}
                animationDuration={10000}
                style={styles.gradientContainer}
            >
                <Login/>
            </GradientBackground>
        </>
    );
}

const styles = StyleSheet.create({
    safe: { 
        flex: 1, 
        backgroundColor: "#000"
    },
    gradientContainer: { 
        justifyContent: "center", 
        alignItems: "center",
        // paddingHorizontal: 30,
        // paddingVertical: 10,
    },
    title: { 
        color: "#fff", 
        fontSize: 24, 
        fontWeight: "bold"
    },
});

export default Screen;