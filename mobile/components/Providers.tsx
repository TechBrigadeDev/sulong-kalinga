import {
    DarkTheme,
    DefaultTheme,
    ThemeProvider,
} from "@react-navigation/native";
import { TamaguiProvider } from "@tamagui/core";
import { PortalProvider } from "@tamagui/portal";
import { ToastProvider } from "@tamagui/toast";
import { QueryClientProvider } from "@tanstack/react-query";
import NotificationProvider from "features/notification/provider";

import { queryClient } from "~/common/query";
import config from "~/tamagui.config";

import { DrawingProvider } from "./drawing/store";
import { GlobalToast } from "./Toast";
import { useColorScheme } from "./useColorScheme.web";

const Providers = ({
    children,
}: {
    children: React.ReactNode;
}) => {
    const colorScheme = useColorScheme();
    return (
        <TamaguiProvider config={config}>
            <PortalProvider shouldAddRootHost>
                <ToastProvider
                    duration={3000}
                    swipeDirection="horizontal"
                    native
                    burntOptions={{
                        from: "top",
                    }}
                >
                    <QueryClientProvider
                        client={queryClient}
                    >
                        <ThemeProvider
                            value={
                                colorScheme ===
                                "dark"
                                    ? DarkTheme
                                    : DefaultTheme
                            }
                        >
                            <NotificationProvider>
                                <DrawingProvider>
                                    {children}
                                </DrawingProvider>
                                <GlobalToast />
                            </NotificationProvider>
                        </ThemeProvider>
                    </QueryClientProvider>
                </ToastProvider>
            </PortalProvider>
        </TamaguiProvider>
    );
};

export default Providers;
