import { Stack } from "expo-router";
import FAQList from "features/portal/faq/list";
import { SafeAreaView } from "react-native-safe-area-context";

const Screen = () => {
    return (
        <SafeAreaView style={{ flex: 1 }}>
            <FAQList />
        </SafeAreaView>
    );
};

const Layout = () => {
    return (
        <>
            <Stack.Screen
                options={{
                    title: "FAQ",
                    headerShown: true,
                    headerTitleAlign: "center",
                }}
            />
            <Screen />
        </>
    );
};

export default Layout;
