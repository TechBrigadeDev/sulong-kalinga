import TabScroll from "components/tabs/TabScroll";
import { Stack } from "expo-router";
import ActiveRequests from "features/portal/emergency-service/_components/active-requests";
import EmergencyServiceFormSelector from "features/portal/emergency-service/_components/form-selector";
import RequestHistory from "features/portal/emergency-service/_components/request-history";
import { EmergencyForm } from "features/portal/emergency-service/emergency/_components/form/form";
import {
    useEmergencyServiceRequests,
    useEmergencyServiceRequestsHistory,
} from "features/portal/emergency-service/hook";
import { ServiceRequestForm } from "features/portal/emergency-service/service/form/form";
import { EmergencyServiceProvider } from "features/portal/emergency-service/store";
import { useEffect, useRef } from "react";
import { RefreshControl } from "react-native";
import { TamaguiElement } from "tamagui";

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

    const ref = useRef<TamaguiElement>(null);

    const reload = () => {
        refetchRequests();
        refetchHistory();
    };

    return (
        <TabScroll
            flex={1}
            display="flex"
            flexDirection="column"
            tabbed
            showScrollUp
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
                ref={ref}
            />
            <ActiveRequests />
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
            <EmergencyForm>
                <ServiceRequestForm>
                    <Screen />
                </ServiceRequestForm>
            </EmergencyForm>
        </EmergencyServiceProvider>
    );
};

export default Layout;
