import { useEmergencyForm } from "features/portal/emergency-service/emergency/_components/form/form";
import { Controller } from "react-hook-form";
import { Input, Label } from "tamagui";

const EmergencyDescription = () => {
    const { control } = useEmergencyForm();
    return (
        <Controller
            control={control}
            name="message"
            render={({ field }) => (
                <>
                    <Label htmlFor="emergency_message">
                        Describe the Emergency
                    </Label>
                    <Input
                        id="emergency_message"
                        value={field.value}
                        onChangeText={
                            field.onChange
                        }
                        placeholder="Briefly describe the situation"
                        size="$4"
                        autoCapitalize="none"
                        autoCorrect={false}
                        maxLength={500}
                        numberOfLines={5}
                        multiline
                    />
                </>
            )}
        />
    );
};

export default EmergencyDescription;
