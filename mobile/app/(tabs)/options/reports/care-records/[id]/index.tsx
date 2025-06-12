import LoadingScreen from "components/loaders/LoadingScreen";
import ScrollView from "components/ScrollView";
import {
    Stack,
    useLocalSearchParams,
} from "expo-router";
import { useWCPRecord } from "features/records/hook";
import WCPRecordDetail from "features/records/wcp/detail";
import { SafeAreaView } from "react-native-safe-area-context";

const Screen = () => {
    const { id } = useLocalSearchParams<{
        id: string;
    }>();

    const { data, isLoading } = useWCPRecord(id);

    if (isLoading) {
        return <LoadingScreen />;
    }

    if (!data) {
        return <LoadingScreen />;
    }

    return <WCPRecordDetail record={data.data} />;
};

const Layout = () => (
    <>
        <Stack.Screen
            options={{
                title: "Care Record",
                headerShown: true,
            }}
        />
        <SafeAreaView style={{ flex: 1 }}>
            <ScrollView
                flex={1}
                paddingBlockEnd={75}
                nestedScrollEnabled
            >
                <Screen />
            </ScrollView>
        </SafeAreaView>
    </>
);

export default Layout;
