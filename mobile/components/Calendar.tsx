import { StyleProp, ViewStyle } from "react-native";
import {
    Calendar as RNCalendar,
    CalendarContextProviderProps,
    CalendarProps,
    CalendarProvider,
    WeekCalendar as RNWeekCalendar,
    WeekCalendarProps,
} from "react-native-calendars";
import { Theme } from "react-native-calendars/src/types";

type CalendarComponentProps = CalendarProps;
interface WeekCalendarComponentProps extends WeekCalendarProps {
    provider: CalendarContextProviderProps;
    containerStyle?: StyleProp<ViewStyle>;
}

export const Calendar = ({ style = {}, theme = {}, ...props }: CalendarComponentProps) => {
    const styles: StyleProp<ViewStyle> = {
        borderRadius: 12,
        padding: 10,
        ...(style as ViewStyle),
    };

    const themes: Theme = {
        textSectionTitleColor: "#000",
        selectedDayBackgroundColor: "#000",
        selectedDayTextColor: "#fff",
        todayTextColor: "#000",
        dayTextColor: "#2d4150",
        textDisabledColor: "#d9e1e8",
        arrowColor: "#000",
        monthTextColor: "#000",
        textMonthFontWeight: "bold",
        textDayFontSize: 16,
        textMonthFontSize: 16,
        textDayHeaderFontSize: 14,
        ...theme,
    };

    return <RNCalendar style={styles} theme={themes} {...props} />;
};

export const WeekCalendar = ({
    provider,
    containerStyle,
    style,
    ...props
}: WeekCalendarComponentProps) => {
    const calendarStyle: StyleProp<ViewStyle> = [
        {
            flex: 1,
            backgroundColor: "red",
        },
        containerStyle,
        style,
    ];
    return (
        <CalendarProvider {...provider}>
            <RNWeekCalendar style={calendarStyle} {...props} />
        </CalendarProvider>
    );
};
