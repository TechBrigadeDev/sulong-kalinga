import { QueryClientProvider } from "@tanstack/react-query";
import { queryClient } from "../common/query";
import { ThemeProvider, DarkTheme, DefaultTheme } from "@react-navigation/native";
import { useColorScheme } from "./useColorScheme.web";

const Providers = ({
    children
}:{
    children: React.ReactNode;
}) => {
    const colorScheme = useColorScheme();
    return (
        <QueryClientProvider client={queryClient}>
            <ThemeProvider value={colorScheme === 'dark' ? DarkTheme : DefaultTheme}>
                {children}
            </ThemeProvider>
        </QueryClientProvider>
    )
}

export default Providers;