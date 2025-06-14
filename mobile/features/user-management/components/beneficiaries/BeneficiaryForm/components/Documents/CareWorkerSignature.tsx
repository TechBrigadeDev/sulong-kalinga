import { Ionicons } from "@expo/vector-icons";
import { useDrawing } from "components/drawing/store";
import { Image } from "expo-image";
import { BeneficiaryFormValues } from "features/user-management/components/beneficiaries/BeneficiaryForm/schema";
import { useEffect, useState } from "react";
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

export const CareWorkerSignature = () => {
    const { onDraw } = useDrawing();
    const { control, setValue, watch } =
        useFormContext<BeneficiaryFormValues>();
    const signatureValue = watch(
        "care_worker_signature",
    );

    const [signature, setSignature] =
        useState<string>("");

    const handleSignature = () => {
        onDraw(
            "/(tabs)/options/user-management/beneficiaries/add",
            (dataUri: string) => {
                setSignature(dataUri);
                setValue(
                    "care_worker_signature",
                    dataUri,
                );
            },
        );
    };

    const handleReset = () => {
        setSignature("");
        setValue("care_worker_signature", "");
    };

    useEffect(() => {
        if (signature) {
            console.log(
                "Care Worker Signature captured:",
                signature,
            );
        }
    }, [signature]);

    return (
        <Controller
            control={control}
            name="care_worker_signature"
            render={({ fieldState }) => (
                <YStack flex={1} gap="$2">
                    <Label fontWeight="600">
                        Care Worker Signature
                    </Label>

                    {/* Signature Preview */}
                    {(signature ||
                        signatureValue) && (
                        <YStack gap="$2">
                            <Image
                                source={{
                                    uri:
                                        signature ||
                                        signatureValue,
                                }}
                                style={{
                                    width: "100%",
                                    height: 100,
                                    borderRadius: 8,
                                    borderWidth: 1,
                                    borderColor:
                                        "#e0e0e0",
                                }}
                                contentFit="contain"
                                transition={200}
                            />
                            <Text
                                fontSize="$2"
                                color="$green10"
                                fontWeight="500"
                            >
                                ✓ Signature
                                captured
                            </Text>
                        </YStack>
                    )}

                    {signature ||
                    signatureValue ? (
                        <Button
                            onPress={handleReset}
                            variant="outlined"
                            theme="red"
                            icon={
                                <Ionicons
                                    name="trash-outline"
                                    size={16}
                                    color="red"
                                />
                            }
                        >
                            Reset
                        </Button>
                    ) : (
                        <Button
                            onPress={
                                handleSignature
                            }
                            variant="outlined"
                        >
                            Sign
                        </Button>
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
