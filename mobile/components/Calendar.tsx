import { StyleProp, ViewStyle } from "react-native";
import { Calendar as RNCalendar, CalendarProps } from "react-native-calendars";
import { Theme } from "react-native-calendars/src/types";

type Props = CalendarProps;

const Calendar = ({
    style = {},
    theme = {},
    ...props
}: Props) => {
    const styles: StyleProp<ViewStyle> = {
        backgroundColor: '#FFF1E6',
        borderRadius: 12,
        padding: 10,
        ...(style as ViewStyle)
    }

    const themes: Theme = {
        backgroundColor: '#FFF1E6',
        calendarBackground: '#FFF1E6',
        textSectionTitleColor: '#000',
        selectedDayBackgroundColor: '#000',
        selectedDayTextColor: '#fff',
        todayTextColor: '#000',
        dayTextColor: '#2d4150',
        textDisabledColor: '#d9e1e8',
        arrowColor: '#000',
        monthTextColor: '#000',
        textMonthFontWeight: 'bold',
        textDayFontSize: 16,
        textMonthFontSize: 16,
        textDayHeaderFontSize: 14,
        ...theme
    }

    return (
        <RNCalendar
            style={styles}
            theme={themes}
            {...props}
        />
    );
}

export default Calendar;
