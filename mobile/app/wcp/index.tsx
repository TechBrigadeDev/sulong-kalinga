import LoadingScreen from "components/loaders/LoadingScreen";
import { Stack } from "expo-router";
import WCPForm from "features/care-plan/form";
import { useGetInterventions } from "features/care-plan/hook";
import {
    SafeAreaView,
    useSafeAreaInsets,
} from "react-native-safe-area-context";

const Screen = () => {
    const insets = useSafeAreaInsets();
    const { isLoading } = useGetInterventions();

    const Form = () =>
        isLoading ? (
            <LoadingScreen />
        ) : (
            <WCPForm />
        );

    return (
        <SafeAreaView style={{ flex: 1 }}>
            <Form />
        </SafeAreaView>
    );
};

const Layout = () => (
    <>
        <Stack.Screen
            options={{
                title: "Weekly Care Plan",
                headerShown: true,
            }}
        />
        <Screen />
    </>
);

export default Layout;
