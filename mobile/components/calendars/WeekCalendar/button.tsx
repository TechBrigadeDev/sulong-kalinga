import RNDateTimePicker from "@react-native-community/datetimepicker";
import { ChevronDown } from "lucide-react-native";
import { useMemo, useState } from "react";
import {
    StyleSheet,
    TouchableOpacity,
} from "react-native";
import { Text, View } from "tamagui";

import { weekCalendarStore } from "./store";

interface Props {
    onPress?: () => void;
    disabled?: boolean;
}

const WeekCalendarButton = ({
    onPress,
    disabled,
}: Props) => {
    const { date, setDate } = weekCalendarStore();

    const [
        isDatePickerVisible,
        setDatePickerVisible,
    ] = useState(false);
    const dateString = useMemo(() => {
        if (!date) return "Select Date";
        const options: Intl.DateTimeFormatOptions =
            {
                weekday: "short",
                month: "short",
                day: "2-digit",
            };
        const formattedDate =
            date.toLocaleDateString(
                "en-US",
                options,
            );
        return formattedDate
            .replace(",", "")
            .replace(/\s+/g, " ")
            .replace(" ", " • ");
    }, [date]);

    const handlePress = () => {
        if (disabled) return;
        setDatePickerVisible(true);
        if (onPress) {
            onPress();
        }
    };

    const Picker = () => {
        if (!isDatePickerVisible) return null;
        return (
            <RNDateTimePicker
                value={date || new Date()}
                mode="date"
                display="default"
                onChange={(
                    event,
                    selectedDate,
                ) => {
                    setDatePickerVisible(false);
                    if (selectedDate) {
                        setDate(
                            new Date(
                                selectedDate,
                            ),
                        );
                    }
                }}
                style={{ width: 0, height: 0 }}
            />
        );
    };

    return (
        <View
            display="flex"
            flexDirection="row"
            justify="center"
            items="center"
            rowGap={8}
        >
            <Picker />
            <TouchableOpacity
                style={styles.button}
                onPressIn={handlePress}
                disabled={disabled}
            >
                <Text
                    fontSize="$4"
                    fontWeight="bold"
                >
                    {dateString}
                </Text>
                <View rounded={"$radius.true"}>
                    <ChevronDown size={16} />
                </View>
            </TouchableOpacity>
        </View>
    );
};

const styles = StyleSheet.create({
    button: {
        padding: 8,
        borderRadius: 8,
        flexDirection: "row",
        alignItems: "center",
        justifyContent: "center",
    },
});

export default WeekCalendarButton;
