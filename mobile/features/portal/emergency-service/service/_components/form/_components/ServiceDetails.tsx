import { FormErrors } from "common/form";
import { useServiceFieldContext } from "features/portal/emergency-service/service/_components/form/form";
import { Input, Label, YStack } from "tamagui";

const ServiceDetails = () => {
    const field = useServiceFieldContext();

    return (
        <YStack gap="$2">
            <Label htmlFor="service_message">
                Service Details
            </Label>
            <Input
                id="service_message"
                value={
                    (field.state
                        .value as string) || ""
                }
                onChangeText={(text) =>
                    field.handleChange(text)
                }
                onBlur={field.handleBlur}
                placeholder="Please describe your service needs..."
                size="$4"
                autoCapitalize="none"
                autoCorrect={false}
                maxLength={500}
                numberOfLines={4}
                multiline
                height={100}
                textAlignVertical="top"
                borderColor={
                    field.state.meta.errors
                        .length > 0
                        ? "$red8"
                        : undefined
                }
            />
            <FormErrors
                errors={field.state.meta.errors}
            />
        </YStack>
    );
};

export default ServiceDetails;
