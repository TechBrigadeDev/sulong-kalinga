import {
    useEffect,
    useMemo,
    useState,
} from "react";
import {
    StyleProp,
    ViewStyle,
} from "react-native";
import {
    CalendarContextProviderProps,
    CalendarProvider,
    WeekCalendar as RNWeekCalendar,
    WeekCalendarProps,
} from "react-native-calendars";
import { View } from "tamagui";

import { weekCalendarStore } from "./store";

interface WeekCalendarComponentProps
    extends Omit<WeekCalendarProps, "date"> {
    provider?: Omit<
        CalendarContextProviderProps,
        "date"
    >;
    containerStyle?: StyleProp<ViewStyle>;
    onDateChanged?: (date: string) => void;
}

const WeekCalendar = ({
    provider,
    containerStyle,
    style,
    ...props
}: WeekCalendarComponentProps) => {
    const { date, setDate } = weekCalendarStore();

    const calendarStyle: StyleProp<ViewStyle> = [
        {
            flex: 1,
        },
        containerStyle,
        style,
    ];

    const [currentDate, setCurrentDate] =
        useState<Date | undefined>(date);
    const dateString = useMemo(() => {
        if (currentDate) {
            return currentDate
                .toISOString()
                .split("T")[0];
        } else {
            return new Date()
                .toISOString()
                .split("T")[0];
        }
    }, [currentDate]);

    useEffect(() => {
        setCurrentDate(date);
    }, [date, setCurrentDate]);

    const onDateChanged = (date: string) => {
        const newDate = new Date(date);
        setCurrentDate(newDate);
        setDate(newDate);
        if (props.onDateChanged) {
            props.onDateChanged(date);
        }
    };

    return (
        <View minH={80} bg={"black"}>
            <CalendarProvider
                date={dateString}
                onDateChanged={onDateChanged}
                {...provider}
            >
                <RNWeekCalendar
                    style={calendarStyle}
                    {...props}
                />
            </CalendarProvider>
        </View>
    );
};

export default WeekCalendar;
