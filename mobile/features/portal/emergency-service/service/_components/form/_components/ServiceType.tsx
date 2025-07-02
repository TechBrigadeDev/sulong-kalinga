import { FormErrors } from "common/form";
import { useServiceFieldContext } from "features/portal/emergency-service/service/_components/form/context";
import { useServiceTypes } from "features/portal/emergency-service/service/hook";
import {
    Adapt,
    Label,
    Select,
    Sheet,
    Spinner,
    YStack,
} from "tamagui";

const ServiceType = () => {
    const { data: serviceTypes, isLoading } =
        useServiceTypes();

    const field = useServiceFieldContext();
    return (
        <YStack flex={1} gap="$2">
            <Label htmlFor="service_type_id">
                Service Type
            </Label>
            <Select
                value={
                    field.state.value as string
                }
                onValueChange={(value) => {
                    console.log(
                        "Selected service type ID:",
                        value,
                        typeof value,
                    );
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
                        <Select.Value placeholder="Select service type" />
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
                        {serviceTypes?.map(
                            (option, index) => (
                                <Select.Item
                                    key={
                                        index.toString() +
                                        option.service_type_id
                                    }
                                    id={
                                        "service_type_id_" +
                                        option.service_type_id
                                    }
                                    index={index}
                                    value={option.service_type_id.toString()}
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

export default ServiceType;
