import TabScroll from "components/tabs/TabScroll";
import { Stack } from "expo-router";
import ActiveRequests from "features/portal/emergency-service/_components/active-requests";
import EmergencyServiceFormSelector from "features/portal/emergency-service/_components/form-selector";
import RequestHistory from "features/portal/emergency-service/_components/request-history";
import { useEmergencyServiceRequests } from "features/portal/emergency-service/hook";
import { useEffect } from "react";

const Screen = () => {
    const { refetch: refetchRequests } =
        useEmergencyServiceRequests();

    useEffect(() => {
        refetchRequests();
    }, [refetchRequests]);

    return (
        <TabScroll
            flex={1}
            display="flex"
            flexDirection="column"
            tabbed
            showScrollUp
            paddingInline={"$4"}
        >
            <EmergencyServiceFormSelector />
            <ActiveRequests />
            <RequestHistory />
        </TabScroll>
    );
};

const Layout = () => {
    return (
        <>
            <Stack.Screen
                options={{
                    headerTitle:
                        "Emergency & Service Request",
                    headerShown: true,
                    headerBackVisible: true,
                }}
            />
            <Screen />
        </>
    );
};

export default Layout;
