import { FormErrors } from "common/form";
import { useEmergencyFieldContext } from "features/portal/emergency-service/emergency/_components/form/context";
import { useEmergencyTypes } from "features/portal/emergency-service/emergency/hook";
import {
    Adapt,
    Label,
    Select,
    Sheet,
    Spinner,
    YStack,
} from "tamagui";

const EmergencyType = () => {
    const { data: emergencyTypes, isLoading } =
        useEmergencyTypes();

    const field = useEmergencyFieldContext();

    return (
        <YStack flex={1} gap="$2">
            <Label htmlFor="emergency_type_id">
                Emergency Type
            </Label>
            <Select
                value={
                    field.state.value as string
                }
                onValueChange={(value) => {
                    field.handleChange(value);
                }}
                disablePreventBodyScroll
            >
                <Select.Trigger
                    disabled={isLoading}
                    borderColor={
                        field.state.meta.errors
                            .length > 0
                            ? "$red8"
                            : undefined
                    }
                >
                    {isLoading ? (
                        <Spinner
                            size="small"
                            mr="$2"
                            color="$white1"
                        />
                    ) : (
                        <Select.Value placeholder="Select emergency type" />
                    )}
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
                            (option, index) => (
                                <Select.Item
                                    key={
                                        index.toString() +
                                        option.emergency_type_id
                                    }
                                    id={
                                        "emergency_type_id_" +
                                        option.emergency_type_id
                                    }
                                    index={index}
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
            <FormErrors
                errors={field.state.meta.errors}
            />
        </YStack>
    );
};

export default EmergencyType;
