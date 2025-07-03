import TabScroll from "components/tabs/TabScroll";
import { Stack } from "expo-router";
import ActiveRequests from "features/portal/emergency-service/_components/active-requests";
import EmergencyServiceFormSelector from "features/portal/emergency-service/_components/form-selector";
import RequestHistory from "features/portal/emergency-service/_components/request-history";
import {
    useEmergencyServiceRequests,
    useEmergencyServiceRequestsHistory,
} from "features/portal/emergency-service/hook";
import { EmergencyServiceProvider } from "features/portal/emergency-service/store";
import { useEffect, useRef } from "react";
import { RefreshControl } from "react-native";
import { ScrollView } from "tamagui";

const Screen = () => {
    const {
        refetch: refetchRequests,
        isRefetching: isRequestRefetching,
    } = useEmergencyServiceRequests();

    const {
        refetch: refetchHistory,
        isRefetching: isHistoryRefetching,
    } = useEmergencyServiceRequestsHistory();

    useEffect(() => {
        refetchRequests();
    }, [refetchRequests]);

    const ref = useRef<ScrollView>(null);

    const reload = () => {
        refetchRequests();
        refetchHistory();
    };

    const onSubmitSuccess = async () => {
        setTimeout(() => {
            if (ref.current) {
                ref.current.scrollTo({
                    y: 400,
                    animated: true,
                });
            }
        }, 300);
    };

    const onEdit = () => {
        if (ref.current) {
            ref.current.scrollTo({
                y: 0,
                animated: true,
            });
        }
    };

    return (
        <TabScroll
            ref={ref}
            flex={1}
            display="flex"
            flexDirection="column"
            tabbed
            paddingInline={"$4"}
            pt="$4"
            refreshControl={
                <RefreshControl
                    refreshing={
                        isRequestRefetching ||
                        isHistoryRefetching
                    }
                    onRefresh={reload}
                />
            }
        >
            <EmergencyServiceFormSelector
                onSubmitSuccess={onSubmitSuccess}
            />
            <ActiveRequests onEdit={onEdit} />
            <RequestHistory />
            <Stack.Screen
                options={{
                    headerTitle:
                        "Emergency & Service Request",
                    headerShown: true,
                    headerBackVisible: true,
                }}
            />
        </TabScroll>
    );
};

const Layout = () => {
    return (
        <EmergencyServiceProvider>
            <Screen />
        </EmergencyServiceProvider>
    );
};

export default Layout;
