import {
    DarkTheme,
    DefaultTheme,
    ThemeProvider,
} from "@react-navigation/native";
import { TamaguiProvider } from "@tamagui/core";
import { PortalProvider } from "@tamagui/portal";
import { QueryClientProvider } from "@tanstack/react-query";

import { queryClient } from "~/common/query";
import config from "~/tamagui.config";

import { useColorScheme } from "./useColorScheme.web";

const Providers = ({
    children,
}: {
    children: React.ReactNode;
}) => {
    const colorScheme = useColorScheme();
    return (
        <TamaguiProvider config={config}>
            <QueryClientProvider
                client={queryClient}
            >
                <ThemeProvider
                    value={
                        colorScheme === "dark"
                            ? DarkTheme
                            : DefaultTheme
                    }
                >
                    <PortalProvider
                        shouldAddRootHost
                    >
                        {children}
                    </PortalProvider>
                </ThemeProvider>
            </QueryClientProvider>
        </TamaguiProvider>
    );
};

export default Providers;
