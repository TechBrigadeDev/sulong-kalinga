import * as DocumentPicker from "expo-document-picker";
import {
    Button,
    Card,
    H3,
    Input,
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

            if (result.type === "success") {
                onChange(field, result.uri);
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
            <YStack p="$4">
                <YStack gap="$4">
                    <YStack>
                        <Text>
                            Upload Beneficiary
                            Picture
                        </Text>
                        <Button
                            onPress={() =>
                                handleFilePick(
                                    "photo",
                                )
                            }
                            theme="gray"
                        >
                            Choose File
                        </Button>
                        {data.photo && (
                            <Text size="$2">
                                File selected
                            </Text>
                        )}
                    </YStack>

                    <YStack>
                        <Text>Review Date</Text>
                        <Input editable={false} />
                    </YStack>

                    <YStack>
                        <Text>
                            Care Service Agreement
                        </Text>
                        <Button
                            onPress={() =>
                                handleFilePick(
                                    "care_service_agreement_doc",
                                )
                            }
                            theme="gray"
                        >
                            Choose File
                        </Button>
                        {data.care_service_agreement_doc && (
                            <Text size="$2">
                                File selected
                            </Text>
                        )}
                    </YStack>

                    <YStack>
                        <Text>
                            General Careplan
                        </Text>
                        <Button
                            onPress={() =>
                                handleFilePick(
                                    "general_care_plan_doc",
                                )
                            }
                            theme="gray"
                        >
                            Choose File
                        </Button>
                        {data.general_care_plan_doc && (
                            <Text size="$2">
                                File selected
                            </Text>
                        )}
                    </YStack>

                    <XStack gap="$4">
                        <YStack flex={1}>
                            <Text>
                                Beneficiary
                                Signature
                            </Text>
                            <Button
                                onPress={() =>
                                    handleSignature(
                                        "beneficiary_signature",
                                    )
                                }
                                theme={
                                    data.beneficiary_signature
                                        ? "green"
                                        : "gray"
                                }
                            >
                                {data.beneficiary_signature
                                    ? "Change Signature"
                                    : "Add Signature"}
                            </Button>
                        </YStack>
                        <YStack flex={1}>
                            <Text>
                                Care Worker
                                Signature
                            </Text>
                            <Button
                                onPress={() =>
                                    handleSignature(
                                        "care_worker_signature",
                                    )
                                }
                                theme={
                                    data.care_worker_signature
                                        ? "green"
                                        : "gray"
                                }
                            >
                                {data.care_worker_signature
                                    ? "Change Signature"
                                    : "Add Signature"}
                            </Button>
                        </YStack>
                    </XStack>
                </YStack>
            </YStack>
        </Card>
    );
};
