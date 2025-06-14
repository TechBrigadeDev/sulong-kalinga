import * as DocumentPicker from "expo-document-picker";
import { BeneficiaryFormValues } from "features/user-management/components/beneficiaries/BeneficiaryForm/schema";
import {
    Controller,
    useFormContext,
} from "react-hook-form";
import {
    Button,
    Label,
    Text,
    YStack,
} from "tamagui";

export const GeneralCarePlan = () => {
    const { control, setValue, watch } =
        useFormContext<BeneficiaryFormValues>();
    const carePlanValue = watch(
        "general_care_plan_doc",
    );

    const handleFilePick = async () => {
        try {
            const result =
                await DocumentPicker.getDocumentAsync(
                    {
                        type: [
                            "image/*",
                            "application/pdf",
                        ],
                        copyToCacheDirectory:
                            true,
                    },
                );

            if (
                !result.canceled &&
                result.assets?.[0]
            ) {
                setValue(
                    "general_care_plan_doc",
                    result.assets[0].uri,
                );
            }
        } catch (err) {
            console.error(
                "Error picking file:",
                err,
            );
        }
    };

    return (
        <Controller
            control={control}
            name="general_care_plan_doc"
            render={({ fieldState }) => (
                <YStack gap="$2">
                    <Label fontWeight="600">
                        General Care Plan
                    </Label>
                    <Button
                        onPress={handleFilePick}
                        variant="outlined"
                    >
                        Choose File
                    </Button>
                    {carePlanValue && (
                        <Text
                            fontSize="$2"
                            color="$green10"
                        >
                            File selected
                        </Text>
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
