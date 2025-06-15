import { useServiceRequestForm } from "features/emergency-service/service/form-hook";
import { Controller } from "react-hook-form";
import {
    Input,
    Label,
    Text,
    YStack,
} from "tamagui";

const ServiceDetails = () => {
    const { control } = useServiceRequestForm();

    return (
        <Controller
            control={control}
            name="service_details"
            render={({ field, fieldState }) => (
                <YStack gap="$2">
                    <Label fontWeight="600">
                        Service Details *
                    </Label>
                    <Input
                        size="$4"
                        value={field.value}
                        onChangeText={
                            field.onChange
                        }
                        onBlur={field.onBlur}
                        placeholder="Please describe your needs..."
                        multiline
                        numberOfLines={4}
                        textAlignVertical="top"
                        borderColor={
                            fieldState.error
                                ? "$red8"
                                : undefined
                        }
                        height={100}
                    />
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

export default ServiceDetails;
