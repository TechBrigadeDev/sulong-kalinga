import {
    Stack,
    useLocalSearchParams,
} from "expo-router";
import CarePlanDetail from "features/portal/care-plan/detail";
import { useCarePlanById } from "features/portal/care-plan/hook";
import { SafeAreaView } from "react-native-safe-area-context";

const Screen = () => {
    const { id } = useLocalSearchParams<{
        id: string;
    }>();

    useCarePlanById(id);
    console.log("Care Plan ID:", id);
    return (
        <SafeAreaView style={{ flex: 1 }}>
            <CarePlanDetail />
        </SafeAreaView>
    );
};

const Layout = () => {
    return (
        <>
            <Stack.Screen
                options={{
                    title: "Care Plan",
                    headerShown: true,
                    headerTitleAlign: "center",
                }}
            />
            <Screen />
        </>
    );
};

export default Layout;
