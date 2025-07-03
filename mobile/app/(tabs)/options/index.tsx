import ProfileSettings from "components/screens/Options/profile/Settings";
import Reports from "components/screens/Options/Reports";
import UserManagement from "components/screens/Options/UserManagement";
import NotificationButton from "features/notification/_components/NotificationButton";
import { StyleSheet } from "react-native";

import Header from "~/components/Header";
import TabScroll from "~/components/tabs/TabScroll";
import LogoutButton from "~/features/auth/components/logout/button";

const Screen = () => {
    return (
        <>
            <Header
                name="Options"
                headerRight={() => (
                    <NotificationButton
                        color="black"
                        items="center"
                        rounded="$radius.true"
                    />
                )}
            />
            <TabScroll
                style={style.scroll}
                contentContainerStyle={{
                    paddingBlockEnd: 200,
                }}
            >
                <ProfileSettings />
                <UserManagement />
                <Reports />
                <LogoutButton />
            </TabScroll>
        </>
    );
};

const style = StyleSheet.create({
    scroll: {
        display: "flex",
        paddingHorizontal: 40,
    },
});

export default Screen;
