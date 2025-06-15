import { WeekCalendar } from "components/calendars";
import WeekCalendarButton from "components/calendars/WeekCalendar/button";
import LoadingScreen from "components/loaders/LoadingScreen";
import { Stack } from "expo-router";
import { useGetVisitations } from "features/portal/visitation/hook";
import VisitationList from "features/portal/visitation/list";
import { portalVisitationListStore } from "features/portal/visitation/list/store";
import { Calendar } from "lucide-react-native";
import { TouchableOpacity } from "react-native";
import { useSafeAreaInsets } from "react-native-safe-area-context";
import { YStack } from "tamagui";

const Screen = () => {
    const { setCurrentDate } =
        portalVisitationListStore();
    const inset = useSafeAreaInsets();

    const { isLoading } = useGetVisitations();

    const List = () =>
        isLoading ? (
            <LoadingScreen />
        ) : (
            <VisitationList />
        );

    const onDateChanged = (date: string) => {
        const newDate = new Date(date);
        setCurrentDate(newDate);
    };

    return (
        <YStack
            flex={1}
            bg="$background"
            marginBlockEnd={inset.bottom}
        >
            <YStack>
                <WeekCalendar
                    onDateChanged={onDateChanged}
                />
                <WeekCalendarButton />
            </YStack>
            <YStack flex={1}>
                <List />
            </YStack>
        </YStack>
    );
};

const HeaderRight = () => {
    return (
        <TouchableOpacity
            style={{
                flex: 1,
                alignSelf: "center",
            }}
        >
            <Calendar
                size={24}
                style={{ marginRight: 16 }}
            />
        </TouchableOpacity>
    );
};

const Layout = () => {
    return (
        <>
            <Stack.Screen
                options={{
                    title: "Visitations",
                    headerShown: true,
                    headerRight: () => (
                        <HeaderRight />
                    ),
                }}
            />
            <Screen />
        </>
    );
};

export default Layout;
