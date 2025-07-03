import { FormErrors } from "common/form";
import { useEmergencyFieldContext } from "features/portal/emergency-service/emergency/_components/form/context";
import { Input, Label, YStack } from "tamagui";

const EmergencyDescription = () => {
    const field = useEmergencyFieldContext();

    return (
        <YStack gap="$2">
            <Label htmlFor="emergency_message">
                Describe the Emergency
            </Label>
            <Input
                id="emergency_message"
                value={
                    field.state.value as string
                }
                onChangeText={(text) =>
                    field.handleChange(text)
                }
                onBlur={field.handleBlur}
                placeholder="Briefly describe the situation"
                size="$4"
                autoCapitalize="none"
                autoCorrect={false}
                maxLength={500}
                numberOfLines={5}
                multiline
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

export default EmergencyDescription;
