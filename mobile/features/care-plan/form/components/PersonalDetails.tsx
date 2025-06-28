import { useCarePlanForm } from "features/care-plan/form/form";
import SelectBeneficiary from "features/user-management/components/beneficiaries/SelectBeneficiary";
import {
    useGetBeneficiaries,
    useGetBeneficiary,
} from "features/user-management/management.hook";
import { IBeneficiary } from "features/user-management/management.type";
import { Info } from "lucide-react-native";
import {
    useEffect,
    useMemo,
    useState,
} from "react";
import { Controller } from "react-hook-form";
import {
    Card,
    Input,
    Label,
    ScrollView,
    Text,
    XStack,
    YStack,
} from "tamagui";

export interface PersonalDetailsData {
    beneficiaryId: string;
    assessment: string;
    bloodPressure: string;
    pulseRate: string;
    temperature: string;
    respiratoryRate: string;
}

interface PersonalDetailsProps {
    data?: PersonalDetailsData;
    onChange?: (
        data: Partial<PersonalDetailsData>,
    ) => void;
}

export const PersonalDetails = ({
    data: _data,
    onChange: _onChange,
}: PersonalDetailsProps) => {
    return (
        <ScrollView>
            <YStack
                style={{ padding: 16, gap: 16 }}
            >
                <Card elevate>
                    <Card.Header padded>
                        <Text
                            style={{
                                fontSize: 20,
                                fontWeight:
                                    "bold",
                            }}
                        >
                            Personal Details
                        </Text>
                    </Card.Header>
                    <YStack p="$4" gap="$4">
                        <Beneficiary />
                        <Illness />
                    </YStack>
                </Card>

                <Card elevate>
                    <Card.Header padded>
                        <Text
                            style={{
                                fontSize: 20,
                                fontWeight:
                                    "bold",
                            }}
                        >
                            Vital Signs
                        </Text>
                    </Card.Header>
                    <YStack p="$4">
                        <YStack gap="$4">
                            <XStack gap="$4">
                                <BloodPressure />
                                <PulseRate />
                            </XStack>
                            <XStack gap="$4">
                                <Temperature />
                                <RespiratoryRate />
                            </XStack>
                        </YStack>
                    </YStack>
                </Card>

                <Card elevate>
                    <Card.Header padded>
                        <Text
                            style={{
                                fontSize: 20,
                                fontWeight:
                                    "bold",
                            }}
                        >
                            Assessment
                        </Text>
                    </Card.Header>
                    <YStack p="$4">
                        <Assessment />
                    </YStack>
                </Card>
            </YStack>
        </ScrollView>
    );
};

const Beneficiary = () => {
    const { record } = useCarePlanFormStore();
    const { data: currentBeneficiary } =
        useGetBeneficiary(
            record?.beneficiary?.beneficiary_id.toString(),
        );

    const { control, getValues } =
        useCarePlanForm();

    const currentBeneficiaryId = getValues(
        "personalDetails.beneficiaryId",
    );

    const [
        selectedBeneficiary,
        setSelectedBeneficiary,
    ] = useState<IBeneficiary | null>(null);

    const { data: beneficiaries } =
        useGetBeneficiaries();

    const allBeneficiaries = useMemo(() => {
        return (
            beneficiaries?.pages?.flatMap(
                (page) => page.data,
            ) || []
        );
    }, [beneficiaries]);

    useEffect(() => {
        if (
            currentBeneficiary?.beneficiary_id.toString() ===
            currentBeneficiaryId
        ) {
            setSelectedBeneficiary(
                currentBeneficiary,
            );
        } else if (!!currentBeneficiaryId) {
            const selected =
                allBeneficiaries.find(
                    (b) =>
                        b.beneficiary_id.toString() ===
                        currentBeneficiaryId,
                );
            setSelectedBeneficiary(
                selected || null,
            );
        }
    }, [
        currentBeneficiary,
        currentBeneficiaryId,
        record?.beneficiary,
        allBeneficiaries,
    ]);

    // get age from beneficiary.birthdate
    const age = selectedBeneficiary
        ? new Date().getFullYear() -
          new Date(
              selectedBeneficiary.birthday,
          ).getFullYear()
        : "";
    const Input = () =>
        record?.beneficiary ? (
            <H4>
                {record.beneficiary.full_name}
            </H4>
        ) : (
            <Controller
                control={control}
                name="personalDetails.beneficiaryId"
                render={({
                    field,
                    fieldState,
                }) => (
                    <>
                        <SelectBeneficiary
                            defaultValue={
                                selectedBeneficiary ||
                                undefined
                            }
                            onValueChange={(
                                beneficiary,
                            ) => {
                                const selected =
                                    allBeneficiaries.find(
                                        (b) =>
                                            b.beneficiary_id.toString() ===
                                            beneficiary?.beneficiary_id.toString(),
                                    );

                                setSelectedBeneficiary(
                                    selected ||
                                        null,
                                );

                                field.onBlur();
                                console.log(
                                    "Selected beneficiary:",
                                    selected,
                                    allBeneficiaries.map(
                                        (b) =>
                                            b.beneficiary_id,
                                    ),
                                    beneficiary?.beneficiary_id,
                                );
                                field.onChange(
                                    selected?.beneficiary_id.toString() ||
                                        null,
                                );
                            }}
                        />
                        {fieldState.error && (
                            <Text
                                color={"$red10"}
                                fontSize="$4"
                                mt="$1"
                                ml="$1"
                            >
                                {
                                    fieldState
                                        .error
                                        .message
                                }
                            </Text>
                        )}
                    </>
                )}
            />
            {selectedBeneficiary && (
                <YStack gap="$2" mt="$2">
                    <Text>Age: {age}</Text>
                    <Text>
                        Birthday:{" "}
                        {new Date(
                            selectedBeneficiary.birthday,
                        ).toLocaleDateString()}
                    </Text>
                    <Text>
                        Gender:{" "}
                        {
                            selectedBeneficiary.gender
                        }
                    </Text>
                    <Text>
                        Civil Status:{" "}
                        {
                            selectedBeneficiary.civil_status
                        }
                    </Text>
                    <Text>
                        Address:{" "}
                        {
                            selectedBeneficiary.street_address
                        }
                    </Text>
                </YStack>
            )}
        </YStack>
    );
};

const Illness = () => {
    const { control } = useCarePlanForm();

    return (
        <YStack gap="$1">
            <Label htmlFor="medicalConditions">
                Illness
            </Label>
            <Controller
                control={control}
                name="personalDetails.illness"
                render={({
                    field,
                    fieldState,
                }) => (
                    <>
                        <Input
                            id="medicalConditions"
                            value={
                                field.value || ""
                            }
                            onBlur={field.onBlur}
                            onChangeText={(
                                text,
                            ) =>
                                field.onChange(
                                    (() => {
                                        console.log(
                                            "Illness changed:",
                                            text,
                                        );
                                        return text;
                                    })(),
                                )
                            }
                            placeholder="e.g. Cough, Fever (separate multiple illnesses with commas)"
                        />
                        {fieldState.error && (
                            <Text
                                color={"$red10"}
                                fontSize="$4"
                                mt="$1"
                                ml="$1"
                            >
                                {
                                    fieldState
                                        .error
                                        .message
                                }
                            </Text>
                        )}
                    </>
                )}
            />
            <XStack
                items="center"
                gap="$2"
                mt="$2"
            >
                <Info size={12} color="#888" />
                <Text
                    style={{
                        color: "#888",
                        fontSize: 12,
                    }}
                >
                    Leave blank if no illness
                    recorded. Separate multiple
                    illnesses with commas.
                </Text>
            </XStack>
        </YStack>
    );
};

const BloodPressure = () => {
    const { control } = useCarePlanForm();

    return (
        <YStack flex={1} gap="$1">
            <Label htmlFor="bloodPressure">
                Blood Pressure *
            </Label>
            <Controller
                control={control}
                name="personalDetails.bloodPressure"
                render={({
                    field,
                    fieldState,
                }) => (
                    <>
                        <Input
                            id="bloodPressure"
                            value={
                                field.value || ""
                            }
                            onBlur={field.onBlur}
                            onChangeText={
                                field.onChange
                            }
                            placeholder="e.g. 120/80"
                        />
                        {fieldState.error && (
                            <Text
                                color={"$red10"}
                                fontSize="$4"
                                mt="$1"
                                ml="$1"
                            >
                                {
                                    fieldState
                                        .error
                                        .message
                                }
                            </Text>
                        )}
                    </>
                )}
            />
        </YStack>
    );
};

const PulseRate = () => {
    const { control } = useCarePlanForm();

    return (
        <YStack flex={1} gap="$1">
            <Label htmlFor="pulseRate">
                Pulse Rate *
            </Label>
            <Controller
                control={control}
                name="personalDetails.pulseRate"
                render={({
                    field,
                    fieldState,
                }) => (
                    <>
                        <Input
                            id="pulseRate"
                            value={
                                field.value?.toString() ||
                                ""
                            }
                            onBlur={field.onBlur}
                            onChangeText={(
                                text,
                            ) => {
                                const numValue =
                                    parseFloat(
                                        text,
                                    );
                                field.onChange(
                                    isNaN(
                                        numValue,
                                    )
                                        ? undefined
                                        : numValue,
                                );
                            }}
                            placeholder="BPM"
                            keyboardType="numeric"
                        />
                        {fieldState.error && (
                            <Text
                                color={"$red10"}
                                fontSize="$4"
                                mt="$1"
                                ml="$1"
                            >
                                {
                                    fieldState
                                        .error
                                        .message
                                }
                            </Text>
                        )}
                    </>
                )}
            />
        </YStack>
    );
};

const Temperature = () => {
    const { control } = useCarePlanForm();

    return (
        <YStack flex={1} gap="$1">
            <Label htmlFor="temperature">
                Temperature *
            </Label>
            <Controller
                control={control}
                name="personalDetails.temperature"
                render={({
                    field,
                    fieldState,
                }) => (
                    <>
                        <Input
                            id="temperature"
                            value={
                                field.value?.toString() ||
                                ""
                            }
                            onBlur={field.onBlur}
                            onChangeText={(
                                text,
                            ) => {
                                const numValue =
                                    parseFloat(
                                        text,
                                    );
                                field.onChange(
                                    isNaN(
                                        numValue,
                                    )
                                        ? undefined
                                        : numValue,
                                );
                            }}
                            placeholder="°C"
                            keyboardType="numeric"
                        />
                        {fieldState.error && (
                            <Text
                                color={"$red10"}
                                fontSize="$4"
                                mt="$1"
                                ml="$1"
                            >
                                {
                                    fieldState
                                        .error
                                        .message
                                }
                            </Text>
                        )}
                    </>
                )}
            />
        </YStack>
    );
};

const RespiratoryRate = () => {
    const { control } = useCarePlanForm();

    return (
        <YStack flex={1} gap="$1">
            <Label htmlFor="respiratoryRate">
                Respiratory Rate *
            </Label>
            <Controller
                control={control}
                name="personalDetails.respiratoryRate"
                render={({
                    field,
                    fieldState,
                }) => (
                    <>
                        <Input
                            id="respiratoryRate"
                            value={
                                field.value?.toString() ||
                                ""
                            }
                            onBlur={field.onBlur}
                            onChangeText={(
                                text,
                            ) => {
                                const numValue =
                                    parseFloat(
                                        text,
                                    );
                                field.onChange(
                                    isNaN(
                                        numValue,
                                    )
                                        ? undefined
                                        : numValue,
                                );
                            }}
                            placeholder="Breaths/min"
                            keyboardType="numeric"
                        />
                        {fieldState.error && (
                            <Text
                                color={"$red10"}
                                fontSize="$4"
                                mt="$1"
                                ml="$1"
                            >
                                {
                                    fieldState
                                        .error
                                        .message
                                }
                            </Text>
                        )}
                    </>
                )}
            />
        </YStack>
    );
};

const Assessment = () => {
    const { control } = useCarePlanForm();

    return (
        <YStack gap="$1">
            <Controller
                control={control}
                name="personalDetails.assessment"
                render={({
                    field,
                    fieldState,
                }) => (
                    <>
                        <Input
                            multiline
                            numberOfLines={4}
                            textAlignVertical="top"
                            value={
                                field.value || ""
                            }
                            onBlur={field.onBlur}
                            onChangeText={
                                field.onChange
                            }
                            placeholder="Enter your assessment here... (minimum 20 characters)"
                        />
                        {fieldState.error && (
                            <Text
                                color={"$red10"}
                                fontSize="$4"
                                mt="$1"
                                ml="$1"
                            >
                                {
                                    fieldState
                                        .error
                                        .message
                                }
                            </Text>
                        )}
                    </>
                )}
            />
        </YStack>
    );
};
