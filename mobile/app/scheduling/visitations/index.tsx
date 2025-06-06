import { WeekCalendar } from "components/calendars";
import WeekCalendarButton from "components/calendars/WeekCalendar/button";
import LoadingScreen from "components/loaders/LoadingScreen";
import ScrollView from "components/ScrollView";
import { Stack } from "expo-router";
import { useVisitations } from "features/scheduling/visitation/hook";
import { useSafeAreaInsets } from "react-native-safe-area-context";
import { Text, YStack } from "tamagui";

const Screen = () => {
    const inset = useSafeAreaInsets();

    const { data, isLoading } = useVisitations();

    const List = () =>
        isLoading ? (
            <LoadingScreen />
        ) : (
            <ScrollView flex={1}>
            </ScrollView>
        );

    return (
        <YStack
            flex={1}
            bg="$background"
            marginBlockEnd={inset.bottom}
        >
            <YStack>
                <WeekCalendar />
                <WeekCalendarButton />
            </YStack>
            <List />
        </YStack>
    );
};

const Layout = () => {
    return (
        <>
            <Stack.Screen
                options={{
                    title: "Visitations",
                    headerShown: true,
                }}
            />
            <Screen />
        </>
    );
};

export default Layout;
