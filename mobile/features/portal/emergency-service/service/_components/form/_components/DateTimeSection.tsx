import { Ionicons } from "@expo/vector-icons";
import {
    DateTimePickerAndroid,
    DateTimePickerEvent,
} from "@react-native-community/datetimepicker";
import { FormErrors } from "common/form";
import { useServiceFieldContext } from "features/portal/emergency-service/service/_components/form/context";
import { Platform } from "react-native";
import {
    Button,
    Input,
    Label,
    YStack,
} from "tamagui";

const PreferredDate = () => {
    const field = useServiceFieldContext();

    const handleDatePress = () => {
        if (Platform.OS === "android") {
            DateTimePickerAndroid.open({
                value: new Date(),
                onChange: (
                    event: DateTimePickerEvent,
                    date?: Date,
                ) => {
                    if (
                        date &&
                        event.type === "set"
                    ) {
                        const formattedDate = date
                            .toISOString()
                            .split("T")[0];
                        field.handleChange(
                            formattedDate,
                        );
                    }
                },
                mode: "date",
                minimumDate: new Date(),
            });
        }
    };

    return (
        <YStack flex={1} gap="$2">
            <Label fontWeight="600">
                Preferred Date *
            </Label>
            {Platform.OS === "ios" ? (
                <Input
                    size="$4"
                    value={
                        field.state
                            .value as string
                    }
                    onChangeText={
                        field.handleChange
                    }
                    onBlur={field.handleBlur}
                    placeholder="mm/dd/yyyy"
                    borderColor={
                        field.state.meta.errors
                            .length > 0
                            ? "$red8"
                            : undefined
                    }
                />
            ) : (
                <Button
                    size="$4"
                    bg="$background"
                    borderColor={
                        field.state.meta.errors
                            .length > 0
                            ? "$red8"
                            : "$borderColor"
                    }
                    borderWidth={1}
                    color="$color"
                    icon={
                        <Ionicons
                            name="calendar-outline"
                            size={20}
                        />
                    }
                    onPress={handleDatePress}
                >
                    {(field.state
                        .value as string) ||
                        "mm/dd/yyyy"}
                </Button>
            )}
            <FormErrors
                errors={field.state.meta.errors}
            />
        </YStack>
    );
};

const PreferredTime = () => {
    const field = useServiceFieldContext();

    const handleTimePress = () => {
        if (Platform.OS === "android") {
            DateTimePickerAndroid.open({
                value: new Date(),
                onChange: (
                    event: DateTimePickerEvent,
                    date?: Date,
                ) => {
                    if (
                        date &&
                        event.type === "set"
                    ) {
                        const formattedTime =
                            date.toLocaleTimeString(
                                [],
                                {
                                    hour: "2-digit",
                                    minute: "2-digit",
                                    hour12: false,
                                },
                            );
                        field.handleChange(
                            formattedTime,
                        );
                    }
                },
                mode: "time",
            });
        }
    };

    return (
        <YStack flex={1} gap="$2">
            <Label fontWeight="600">
                Preferred Time *
            </Label>
            {Platform.OS === "ios" ? (
                <Input
                    size="$4"
                    value={
                        field.state
                            .value as string
                    }
                    onChangeText={
                        field.handleChange
                    }
                    onBlur={field.handleBlur}
                    placeholder="--:-- --"
                    borderColor={
                        field.state.meta.errors
                            .length > 0
                            ? "$red8"
                            : undefined
                    }
                />
            ) : (
                <Button
                    size="$4"
                    bg="$background"
                    borderColor={
                        field.state.meta.errors
                            .length > 0
                            ? "$red8"
                            : "$borderColor"
                    }
                    borderWidth={1}
                    color="$color"
                    icon={
                        <Ionicons
                            name="time-outline"
                            size={20}
                        />
                    }
                    onPress={handleTimePress}
                >
                    {(field.state
                        .value as string) ||
                        "--:-- --"}
                </Button>
            )}
            <FormErrors
                errors={field.state.meta.errors}
            />
        </YStack>
    );
};

// This component handles both date and time fields
// It will be called twice, once for each field
const DateTimeSection = () => {
    const field = useServiceFieldContext();

    // Determine which field we're rendering based on the field name
    if (field.name === "service_date") {
        return <PreferredDate />;
    } else if (field.name === "service_time") {
        return <PreferredTime />;
    }

    return null;
};

export default DateTimeSection;
