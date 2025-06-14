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

export const CareServiceAgreement = () => {
    const { control, setValue, watch } =
        useFormContext<BeneficiaryFormValues>();
    const agreementValue = watch(
        "care_service_agreement_doc",
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
                    "care_service_agreement_doc",
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
            name="care_service_agreement_doc"
            render={({ fieldState }) => (
                <YStack gap="$2">
                    <Label fontWeight="600">
                        Care Service Agreement
                    </Label>
                    <Button
                        onPress={handleFilePick}
                        variant="outlined"
                    >
                        Choose File
                    </Button>
                    {agreementValue && (
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
