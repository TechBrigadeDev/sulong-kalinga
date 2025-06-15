import { useServiceRequestForm } from "features/emergency-service/service/form-hook";
import { Controller } from "react-hook-form";
import {
    Adapt,
    Label,
    Select,
    Sheet,
    Text,
    YStack,
} from "tamagui";

const serviceTypes = [
    { label: "Other Service", value: "other" },
    {
        label: "Home Care",
        value: "home_care",
    },
    {
        label: "Medical Assistance",
        value: "medical_assistance",
    },
    {
        label: "Transportation",
        value: "transportation",
    },
    { label: "Counseling", value: "counseling" },
    {
        label: "Equipment Rental",
        value: "equipment_rental",
    },
    {
        label: "Therapy Services",
        value: "therapy_services",
    },
];

const ServiceType = () => {
    const { control } = useServiceRequestForm();

    return (
        <Controller
            control={control}
            name="service_type"
            render={({ field, fieldState }) => (
                <YStack gap="$2">
                    <Label fontWeight="600">
                        Service Type *
                    </Label>
                    <Select
                        value={field.value || ""}
                        onValueChange={
                            field.onChange
                        }
                    >
                        <Select.Trigger>
                            <Select.Value placeholder="Select service type" />
                        </Select.Trigger>
                        
                        <Adapt
                            when="maxMd"
                            platform="touch"
                        >
                            <Sheet
                                modal
                                dismissOnSnapToBottom
                            >
                                <Sheet.Frame>
                                    <Sheet.ScrollView>
                                        <Adapt.Contents />
                                    </Sheet.ScrollView>
                                </Sheet.Frame>
                                <Sheet.Overlay
                                    animation="lazy"
                                    enterStyle={{
                                        opacity: 0,
                                    }}
                                    exitStyle={{
                                        opacity: 0,
                                    }}
                                />
                            </Sheet>
                        </Adapt>

                        <Select.Content
                            zIndex={200000}
                        >
                            <Select.ScrollUpButton />
                            <Select.Viewport
                                bg="$accent1"
                                p="$2"
                                style={{
                                    maxHeight: 300,
                                    minHeight: 200,
                                }}
                            >
                                {serviceTypes.map(
                                    (
                                        option,
                                        index,
                                    ) => (
                                        <Select.Item
                                            key={
                                                option.value
                                            }
                                            index={
                                                index
                                            }
                                            value={
                                                option.value
                                            }
                                        >
                                            <Select.ItemText>
                                                {
                                                    option.label
                                                }
                                            </Select.ItemText>
                                        </Select.Item>
                                    ),
                                )}
                            </Select.Viewport>
                            <Select.ScrollDownButton />
                        </Select.Content>
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

export default ServiceType;
