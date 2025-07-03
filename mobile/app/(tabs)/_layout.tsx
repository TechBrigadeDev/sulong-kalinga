import TabButton from "components/screens/Home/_components/button";
import { portalMenuItems } from "components/screens/Home/paths";
import {
    useNavigation,
    useRouter,
    useSegments,
} from "expo-router";
import {
    TabList,
    Tabs,
    TabSlot,
    TabTrigger,
} from "expo-router/ui";
import { isPortal } from "features/auth/auth.util";
import { useCallback } from "react";
import { StyleSheet } from "react-native";
import { useSafeAreaInsets } from "react-native-safe-area-context";
import { View } from "tamagui";

export default function Layout() {
    const insets = useSafeAreaInsets();
    const router = useRouter();
    const segments = useSegments();

    // Handle options tab re-press to redirect to profile
    const handleOptionsTabPress =
        useCallback(() => {
            const currentSegment = segments[1]; // Get current tab segment
            const isOnOptionsTab =
                currentSegment === "options";

            if (isOnOptionsTab) {
                // Already on options tab - redirect to profile
                router.replace("/(tabs)/options");
            }
            // Otherwise, let default navigation happen
        }, [router, segments]);

    return (
        <Tabs
            options={{
                screenOptions: {
                    lazy: true,
                    action: {
                        type: "none",
                    },
                    title: "",
                },
            }}
        >
            <View style={styles.tabContainer}>
                <TabSlot />
            </View>
            <TabList asChild>
                <View
                    style={{
                        ...styles.tabList,
                        marginBottom:
                            insets.bottom,
                    }}
                >
                    <TabButton
                        icon="MessageCircle"
                        onPressIn={() =>
                            router.push(
                                "/messaging",
                            )
                        }
                    >
                        Messaging
                    </TabButton>
                    <TabTrigger
                        name="(tabs)/index"
                        href="/(tabs)"
                        asChild
                    >
                        <TabButton icon="House">
                            Home
                        </TabButton>
                    </TabTrigger>
                    <TabTrigger
                        name="/(tabs)/options/index"
                        href="/(tabs)/options"
                        reset="always"
                        onPress={
                            handleOptionsTabPress
                        }
                        asChild
                    >
                        <TabButton icon="EllipsisVertical">
                            Options
                        </TabButton>
                    </TabTrigger>
                    {/* <TabTrigger
                        name="/(tabs)/reports/care-records/index"
                        href="/(tabs)/options/reports/care-records"
                        style={{
                            display: "none",
                        }}
                    /> */}
                    {/* <TabTrigger
                        name="/(tabs)/reports/user-management/index"
                        href="/(tabs)/options/reports/user-management/"
                        style={{
                            display: "none",
                        }}
                    /> */}
                    <TabTrigger
                        name="/(tabs)/shifts/index"
                        href="/(tabs)/shifts"
                        style={{
                            display: "none",
                        }}
                    />
                    {isPortal() &&
                        portalMenuItems.map(
                            (item, idx) => (
                                <TabTrigger
                                    key={idx}
                                    name={
                                        item.name
                                    }
                                    href={
                                        item.href
                                    }
                                    style={{
                                        display:
                                            "none",
                                    }}
                                />
                            ),
                        )}
                </View>
            </TabList>
        </Tabs>
    );
}

const styles = StyleSheet.create({
    tabContainer: {
        flex: 1,
    },
    tabList: {
        flexDirection: "row",
        justifyContent: "space-around",
        paddingVertical: 10,
        backgroundColor: "#ffffff",

        position: "absolute",
        bottom: 0,
        left: 20,
        right: 20,

        borderRadius: 50,
        shadowColor: "#000",
        shadowOffset: {
            width: 0,
            height: 5,
        },
        shadowOpacity: 0.2,
        shadowRadius: 5,
    },
});
