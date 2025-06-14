import { Ionicons } from "@expo/vector-icons";
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

export const BeneficiaryPhoto = () => {
    const { control, setValue, watch } =
        useFormContext<BeneficiaryFormValues>();
    const photoValue = watch("photo");

    const handleFilePick = async () => {
        try {
            const result =
                await DocumentPicker.getDocumentAsync(
                    {
                        type: ["image/*"],
                        copyToCacheDirectory:
                            true,
                        multiple: false,
                    },
                );

            if (
                !result.canceled &&
                result.assets?.[0]
            ) {
                setValue(
                    "photo",
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
            name="photo"
            render={({ fieldState }) => (
                <YStack gap="$2">
                    <Label fontWeight="600">
                        Upload Beneficiary Picture
                        *
                    </Label>
                    <Button
                        onPress={handleFilePick}
                        variant="outlined"
                        size="$4"
                        theme={
                            photoValue
                                ? "green"
                                : undefined
                        }
                        icon={
                            <Ionicons
                                name={
                                    photoValue
                                        ? "checkmark-circle"
                                        : "camera"
                                }
                                size={20}
                                color={
                                    photoValue
                                        ? "white"
                                        : "gray"
                                }
                            />
                        }
                    >
                        {photoValue
                            ? "Photo Selected"
                            : "Take/Choose Photo"}
                    </Button>
                    {photoValue && (
                        <Text
                            fontSize="$2"
                            color="$green10"
                            fontWeight="500"
                        >
                            ✓ Photo ready for
                            upload
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
