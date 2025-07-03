import "react-native-reanimated";

import FontAwesome from "@expo/vector-icons/FontAwesome";
import Dialogs from "components/dialogs/Dialogs";
import { Toast } from "components/Toast";
import { useFonts } from "expo-font";
import { Stack } from "expo-router";
import * as SplashScreen from "expo-splash-screen";
import { useEffect } from "react";
import { GestureHandlerRootView } from "react-native-gesture-handler";

import Providers from "~/components/Providers";
import { authStore } from "~/features/auth/auth.store";
import { notificationHandler } from "~/features/notification/service";

export {
    // Catch any errors thrown by the Layout component.
    ErrorBoundary,
} from "expo-router";

export const unstable_settings = {
    // Ensure that reloading on `/modal` keeps a back button present.
    initialRouteName: "(drawer)",
};

// Prevent the splash screen from auto-hiding before asset loading is complete.
SplashScreen.preventAutoHideAsync();

notificationHandler();

export default function RootLayout() {
    const [loaded, error] = useFonts({
        SpaceMono: require("~/assets/fonts/SpaceMono-Regular.ttf"),
        ...FontAwesome.font,
    });

    // Expo Router uses Error Boundaries to catch errors in the navigation tree.
    useEffect(() => {
        if (error) throw error;
    }, [error]);

    useEffect(() => {
        if (loaded) {
            SplashScreen.hideAsync();
        }
    }, [loaded]);

    if (!loaded) {
        return null;
    }

    return <RootLayoutNav />;
}

function RootLayoutNav() {
    const isAuthenticated =
        authStore((state) => state.token) !==
        null;

    return (
        <Providers>
            <GestureHandlerRootView
                style={{ flex: 1 }}
            >
                <Stack>
                    <Stack.Screen name="login" />
                    <Stack.Protected
                        guard={isAuthenticated}
                    >
                        <Stack.Protected
                            guard={
                                isAuthenticated
                            }
                        >
                            <Stack.Screen
                                name="(tabs)"
                                options={{
                                    headerShown:
                                        false,
                                }}
                            />
                            <Stack.Screen
                                name="wcp/index"
                                options={{
                                    title: "Care Plan",
                                    headerTitle:
                                        "Care Plan",
                                }}
                            />
                            <Stack.Screen
                                name="messaging"
                                options={{
                                    animation:
                                        "fade",
                                    headerShown:
                                        false,
                                }}
                            />
                            <Stack.Screen
                                name="scheduling"
                                options={{
                                    headerShown:
                                        false,
                                }}
                            />
                        </Stack.Protected>
                    </Stack.Protected>
                </Stack>
            </GestureHandlerRootView>
            <Dialogs />
            <Toast />
        </Providers>
    );
}
