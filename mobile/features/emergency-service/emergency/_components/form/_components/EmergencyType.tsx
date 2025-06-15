import { useEmergencyForm } from "features/emergency-service/emergency/_components/form/form";
import { useEmergencyTypes } from "features/emergency-service/emergency/hook";
import { Controller } from "react-hook-form";
import {
    Adapt,
    Label,
    Select,
    Sheet,
    Text,
    YStack,
} from "tamagui";

const EmergencyType = () => {
    const { data: emergencyTypes, isLoading } =
        useEmergencyTypes();

    const { control } = useEmergencyForm();
    const disabled = isLoading || !emergencyTypes;

    return (
        <Controller
            control={control}
            disabled={disabled}
            name="emergency_type_id"
            render={({ field, fieldState }) => (
                <YStack flex={1} gap="$2">
                    <Label htmlFor="emergency_type_id">
                        Emergency Type
                    </Label>
                    <Select
                        value={field.value || ""}
                        onValueChange={
                            field.onChange
                        }
                    >
                        <Select.Trigger>
                            <Select.Value placeholder="Select status" />
                        </Select.Trigger>
                        <Select.Content>
                            <Select.ScrollUpButton />
                            <Select.Viewport
                                bg="$accent1"
                                p="$2"
                                style={{
                                    maxHeight: 300,
                                    minHeight: 200,
                                }}
                            >
                                {emergencyTypes?.map(
                                    (
                                        option,
                                        index,
                                    ) => (
                                        <Select.Item
                                            key={
                                                index.toString() +
                                                option.emergency_type_id
                                            }
                                            id={
                                                "emergency_type_id_" +
                                                option.emergency_type_id
                                            }
                                            index={
                                                index
                                            }
                                            value={option.emergency_type_id.toString()}
                                        >
                                            <Select.ItemText>
                                                {
                                                    option.name
                                                }
                                            </Select.ItemText>
                                        </Select.Item>
                                    ),
                                )}
                            </Select.Viewport>
                        </Select.Content>
                        <Select.Adapt
                            when="maxMd"
                            platform="touch"
                        >
                            <Sheet
                                modal
                                animation="quicker"
                            >
                                <Sheet.Frame>
                                    <Adapt.Contents />
                                </Sheet.Frame>
                                <Sheet.Overlay bg="transparent" />
                            </Sheet>
                        </Select.Adapt>
                    </Select>
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

export default EmergencyType;
