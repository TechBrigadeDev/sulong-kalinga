import { TamaguiProvider } from "@tamagui/core";
import { QueryClientProvider } from "@tanstack/react-query";
import { queryClient } from "../common/query";
import {
  ThemeProvider,
  DarkTheme,
  DefaultTheme,
} from "@react-navigation/native";
import { useColorScheme } from "./useColorScheme.web";
import config from "../tamagui.config";

const Providers = ({ children }: { children: React.ReactNode }) => {
  const colorScheme = useColorScheme();
  return (
    <TamaguiProvider config={config}>
      <QueryClientProvider client={queryClient}>
        <ThemeProvider
          value={colorScheme === "dark" ? DarkTheme : DefaultTheme}
        >
          {children}
        </ThemeProvider>
      </QueryClientProvider>
    </TamaguiProvider>
  );
};

export default Providers;
