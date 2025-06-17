import { authStore } from "features/auth/auth.store";
import NotificationButton from "features/notification/_components/NotificationButton";
import { StyleSheet } from "react-native";
import { useSafeAreaInsets } from "react-native-safe-area-context";
import { Avatar, H3, YStack } from "tamagui";

import GradientBackground from "~/components/GradientContainer";
import { topBarHeight } from "~/constants/Layout";
import UserAvatar from "~/features/user/components/UserAvatar";

const Profile = () => {
    const { top: topBarHeight } =
        useSafeAreaInsets();

    const { user } = authStore();

    return (
        <GradientBackground>
            <YStack
                flex={1}
                // items="center"
                content="center"
                marginBlockStart={
                    topBarHeight + 5
                }
                paddingBlockEnd={30}
            >
                <NotificationButton
                    color="#fff"
                    position="absolute"
                    // t={topBarHeight}
                    r={0}
                    mt={10}
                    mr={20}
                    aria-label="Notifications"
                />
                <YStack
                    items="center"
                    content="center"
                    marginBlockStart={
                        topBarHeight + 20
                    }
                >
                    <Avatar
                        circular
                        size="$10"
                        marginBottom={10}
                    >
                        <UserAvatar />
                    </Avatar>
                    <H3 style={style.name}>
                        {user?.first_name}{" "}
                        {user?.last_name}
                    </H3>
                </YStack>
            </YStack>
        </GradientBackground>
    );
};

const style = StyleSheet.create({
    container: {
        flex: 1,
        alignItems: "center",
        justifyContent: "center",
        marginTop: topBarHeight,
        paddingBottom: 30,
    },
    name: {
        fontWeight: "bold",
        color: "#fff",
        marginBottom: 10,
    },
    shadow: {
        shadowColor: "#000",
        shadowOffset: {
            width: 0,
            height: 1,
        },
        shadowOpacity: 0.2,
        shadowRadius: 1.41,
    },
});

export default Profile;
