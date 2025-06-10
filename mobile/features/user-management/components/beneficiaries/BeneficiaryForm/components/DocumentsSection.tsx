import * as DocumentPicker from "expo-document-picker";
import {
    Button,
    Card,
    H3,
    Input,
    Label,
    Text,
    XStack,
    YStack,
} from "tamagui";

import { useSignatureStore } from "~/components/dialogs/signature/store";
import { IBeneficiary } from "~/features/user-management/management.type";

interface Props {
    data?: Partial<IBeneficiary>;
    onChange?: (
        field: string | number | symbol,
        value: any,
    ) => void;
}

export const DocumentsSection = ({
    data = {},
    onChange = () => {},
}: Props) => {
    const { setIsOpen, setTitle, setOnSave } =
        useSignatureStore();

    const handleFilePick = async (
        field: keyof IBeneficiary,
    ) => {
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
                onChange(
                    field,
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

    const handleSignature = (
        field: keyof IBeneficiary,
    ) => {
        setTitle(
            field === "beneficiary_signature"
                ? "Beneficiary Signature"
                : "Care Worker Signature",
        );
        setOnSave((signature) =>
            onChange(field, signature),
        );
        setIsOpen(true);
    };

    return (
        <Card elevate>
            <Card.Header padded>
                <H3>Documents and Signatures</H3>
            </Card.Header>
            <YStack p="$4" gap="$4">
                <YStack gap="$2">
                    <Label fontWeight="600">
                        Upload Beneficiary Picture
                    </Label>
                    <Button
                        onPress={() =>
                            handleFilePick(
                                "photo",
                            )
                        }
                        variant="outlined"
                    >
                        Choose File
                    </Button>
                    {data.photo && (
                        <Text
                            fontSize="$2"
                            color="$green10"
                        >
                            File selected
                        </Text>
                    )}
                </YStack>

                <YStack gap="$2">
                    <Label fontWeight="600">
                        Review Date
                    </Label>
                    <Input
                        size="$4"
                        editable={false}
                    />
                </YStack>

                <YStack gap="$2">
                    <Label fontWeight="600">
                        Care Service Agreement
                    </Label>
                    <Button
                        onPress={() =>
                            handleFilePick(
                                "care_service_agreement_doc",
                            )
                        }
                        variant="outlined"
                    >
                        Choose File
                    </Button>
                    {data.care_service_agreement_doc && (
                        <Text
                            fontSize="$2"
                            color="$green10"
                        >
                            File selected
                        </Text>
                    )}
                </YStack>

                <YStack gap="$2">
                    <Label fontWeight="600">
                        General Care Plan
                    </Label>
                    <Button
                        onPress={() =>
                            handleFilePick(
                                "general_care_plan_doc",
                            )
                        }
                        variant="outlined"
                    >
                        Choose File
                    </Button>
                    {data.general_care_plan_doc && (
                        <Text
                            fontSize="$2"
                            color="$green10"
                        >
                            File selected
                        </Text>
                    )}
                </YStack>

                <XStack gap="$4">
                    <YStack flex={1} gap="$2">
                        <Label fontWeight="600">
                            Beneficiary Signature
                        </Label>
                        <Button
                            onPress={() =>
                                handleSignature(
                                    "beneficiary_signature",
                                )
                            }
                            theme={
                                data.beneficiary_signature
                                    ? "green"
                                    : undefined
                            }
                            variant="outlined"
                        >
                            {data.beneficiary_signature
                                ? "Signed"
                                : "Sign"}
                        </Button>
                    </YStack>

                    <YStack flex={1} gap="$2">
                        <Label fontWeight="600">
                            Care Worker Signature
                        </Label>
                        <Button
                            onPress={() =>
                                handleSignature(
                                    "care_worker_signature",
                                )
                            }
                            theme={
                                data.care_worker_signature
                                    ? "green"
                                    : undefined
                            }
                            variant="outlined"
                        >
                            {data.care_worker_signature
                                ? "Signed"
                                : "Sign"}
                        </Button>
                    </YStack>
                </XStack>
            </YStack>
        </Card>
    );
};
