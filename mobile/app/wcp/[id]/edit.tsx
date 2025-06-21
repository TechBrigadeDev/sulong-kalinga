import LoadingScreen from "components/loaders/LoadingScreen";
import {
    Stack,
    useLocalSearchParams,
} from "expo-router";
import WCPForm from "features/care-plan/form";
import { useGetInterventions } from "features/care-plan/hook";
import { useWCPRecord } from "features/records/hook";
import {
    SafeAreaProvider,
    useSafeAreaInsets,
} from "react-native-safe-area-context";

const Screen = () => {
    const { id } = useLocalSearchParams<{
        id: string;
    }>();

    const insets = useSafeAreaInsets();
    const { data, isLoading: isRecordLoading } =
        useWCPRecord(id);
    const { isLoading: interventionsLoading } =
        useGetInterventions();

    const isLoading =
        isRecordLoading || interventionsLoading;

    const Form = () =>
        isLoading ? (
            <LoadingScreen />
        ) : (
            <WCPForm record={data?.data} />
        );

    return (
        <SafeAreaProvider
            style={{
                flex: 1,
                marginBottom: insets.bottom,
                alignItems: "center",
            }}
        >
            <Form />
        </SafeAreaProvider>
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
