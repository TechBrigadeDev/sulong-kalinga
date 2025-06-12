import SelectBeneficiary from "features/user-management/components/beneficiaries/SelectBeneficiary";
import { IBeneficiary } from "features/user-management/management.type";
import { useState } from "react";
import {
    Card,
    Input,
    Label,
    ScrollView,
    Text,
    XStack,
    YStack,
} from "tamagui";
export interface Beneficiary {
    id: string;
    name: string;
    age: string;
    gender: string;
    medicalConditions: string;
}

export interface PersonalDetailsData {
    beneficiaryId: string;
    assessment: string;
    bloodPressure: string;
    pulseRate: string;
    temperature: string;
    respiratoryRate: string;
}

interface PersonalDetailsProps {
    data: PersonalDetailsData;
    onChange: (
        data: Partial<PersonalDetailsData>,
    ) => void;
}

export const PersonalDetails = ({
    data,
    onChange,
}: PersonalDetailsProps) => {
    const [
        selectedBeneficiary,
        setSelectedBeneficiary,
    ] = useState<IBeneficiary | null>(null);

    // get age from beneficiary.birthdate
    const age = selectedBeneficiary
        ? new Date().getFullYear() -
          new Date(
              selectedBeneficiary.birthday,
          ).getFullYear()
        : "";
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
                            Select Beneficiary
                        </Text>
                    </Card.Header>
                    <YStack p="$4">
                        <YStack
                            style={{ gap: 16 }}
                        >
                            <SelectBeneficiary
                                onValueChange={
                                    setSelectedBeneficiary
                                }
                            />
                            {selectedBeneficiary && (
                                <YStack gap="$2">
                                    <Text>
                                        Age: {age}
                                    </Text>
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
                                        Civil
                                        Status:{" "}
                                        {
                                            selectedBeneficiary.civil_status
                                        }
                                    </Text>
                                    <Text>
                                        Address:{" "}
                                        {}
                                    </Text>
                                </YStack>
                            )}
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
                            Vital Signs
                        </Text>
                    </Card.Header>
                    <YStack p="$4">
                        <YStack gap="$4">
                            <XStack gap="$4">
                                <YStack flex={1}>
                                    <Label htmlFor="bloodPressure">
                                        Blood
                                        Pressure
                                    </Label>
                                    <Input
                                        id="bloodPressure"
                                        value={
                                            data.bloodPressure
                                        }
                                        onChangeText={(
                                            text,
                                        ) =>
                                            onChange(
                                                {
                                                    bloodPressure:
                                                        text,
                                                },
                                            )
                                        }
                                        placeholder="e.g. 120/80"
                                    />
                                </YStack>
                                <YStack flex={1}>
                                    <Label htmlFor="pulseRate">
                                        Pulse Rate
                                    </Label>
                                    <Input
                                        id="pulseRate"
                                        value={
                                            data.pulseRate
                                        }
                                        onChangeText={(
                                            text,
                                        ) =>
                                            onChange(
                                                {
                                                    pulseRate:
                                                        text,
                                                },
                                            )
                                        }
                                        placeholder="BPM"
                                    />
                                </YStack>
                            </XStack>

                            <XStack gap="$4">
                                <YStack flex={1}>
                                    <Label htmlFor="temperature">
                                        Temperature
                                    </Label>
                                    <Input
                                        id="temperature"
                                        value={
                                            data.temperature
                                        }
                                        onChangeText={(
                                            text,
                                        ) =>
                                            onChange(
                                                {
                                                    temperature:
                                                        text,
                                                },
                                            )
                                        }
                                        placeholder="°C"
                                    />
                                </YStack>
                                <YStack flex={1}>
                                    <Label htmlFor="respiratoryRate">
                                        Respiratory
                                        Rate
                                    </Label>
                                    <Input
                                        id="respiratoryRate"
                                        value={
                                            data.respiratoryRate
                                        }
                                        onChangeText={(
                                            text,
                                        ) =>
                                            onChange(
                                                {
                                                    respiratoryRate:
                                                        text,
                                                },
                                            )
                                        }
                                        placeholder="Breaths/min"
                                    />
                                </YStack>
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
                        <Input
                            multiline
                            numberOfLines={4}
                            textAlignVertical="top"
                            value={
                                data.assessment
                            }
                            onChangeText={(
                                text,
                            ) =>
                                onChange({
                                    assessment:
                                        text,
                                })
                            }
                            placeholder="Enter your assessment here..."
                        />
                    </YStack>
                </Card>
            </YStack>
        </ScrollView>
    );
};
