import { Ionicons } from "@expo/vector-icons";
import {
    DateTimePickerAndroid,
    DateTimePickerEvent,
} from "@react-native-community/datetimepicker";
import { useServiceRequestForm } from "features/emergency-service/service/form/form";
import { Controller } from "react-hook-form";
import { Platform } from "react-native";
import {
    Button,
    Input,
    Label,
    Text,
    XStack,
    YStack,
} from "tamagui";

const PreferredDate = () => {
    const { control } = useServiceRequestForm();

    const handleDatePress = (
        onChange: (value: string) => void,
    ) => {
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
                        onChange(formattedDate);
                    }
                },
                mode: "date",
                minimumDate: new Date(),
            });
        }
    };

    return (
        <Controller
            control={control}
            name="preferred_date"
            render={({ field, fieldState }) => (
                <YStack flex={1} gap="$2">
                    <Label fontWeight="600">
                        Preferred Date *
                    </Label>
                    {Platform.OS === "ios" ? (
                        <Input
                            size="$4"
                            value={field.value}
                            onChangeText={
                                field.onChange
                            }
                            placeholder="mm/dd/yyyy"
                            borderColor={
                                fieldState.error
                                    ? "$red8"
                                    : undefined
                            }
                        />
                    ) : (
                        <Button
                            size="$4"
                            backgroundColor="$background"
                            borderColor={
                                fieldState.error
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
                            onPress={() =>
                                handleDatePress(
                                    field.onChange,
                                )
                            }
                        >
                            {field.value ||
                                "mm/dd/yyyy"}
                        </Button>
                    )}
                    {fieldState.error && (
                        <Text
                            color="$red10"
                            fontSize="$2"
                        >
                            {
                                fieldState.error
                                    .message
                            }
                        </Text>
                    )}
                </YStack>
            )}
        />
    );
};

const PreferredTime = () => {
    const { control } = useServiceRequestForm();

    const handleTimePress = (
        onChange: (value: string) => void,
    ) => {
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
                        onChange(formattedTime);
                    }
                },
                mode: "time",
            });
        }
    };

    return (
        <Controller
            control={control}
            name="preferred_time"
            render={({ field, fieldState }) => (
                <YStack flex={1} gap="$2">
                    <Label fontWeight="600">
                        Preferred Time *
                    </Label>
                    {Platform.OS === "ios" ? (
                        <Input
                            size="$4"
                            value={field.value}
                            onChangeText={
                                field.onChange
                            }
                            placeholder="--:-- --"
                            borderColor={
                                fieldState.error
                                    ? "$red8"
                                    : undefined
                            }
                        />
                    ) : (
                        <Button
                            size="$4"
                            backgroundColor="$background"
                            borderColor={
                                fieldState.error
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
                            onPress={() =>
                                handleTimePress(
                                    field.onChange,
                                )
                            }
                        >
                            {field.value ||
                                "--:-- --"}
                        </Button>
                    )}
                    {fieldState.error && (
                        <Text
                            color="$red10"
                            fontSize="$2"
                        >
                            {
                                fieldState.error
                                    .message
                            }
                        </Text>
                    )}
                </YStack>
            )}
        />
    );
};

const DateTimeSection = () => {
    return (
        <XStack gap="$3">
            <PreferredDate />
            <PreferredTime />
        </XStack>
    );
};

export default DateTimeSection;
