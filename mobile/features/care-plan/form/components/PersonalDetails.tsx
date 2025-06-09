import {
    Card,
    Input,
    Label,
    ScrollView,
    Select,
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

const BENEFICIARIES: Beneficiary[] = [
    {
        id: "1",
        name: "Juan Dela Cruz",
        age: "75",
        gender: "Male",
        medicalConditions:
            "Hypertension, Type 2 Diabetes",
    },
    {
        id: "2",
        name: "Maria Santos",
        age: "68",
        gender: "Female",
        medicalConditions: "Arthritis",
    },
];

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
    const selectedBeneficiary = data.beneficiaryId
        ? BENEFICIARIES.find(
              (b) => b.id === data.beneficiaryId,
          )
        : undefined;

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
                            <Select
                                value={
                                    data.beneficiaryId
                                }
                                onValueChange={(
                                    value: string,
                                ) =>
                                    onChange({
                                        beneficiaryId:
                                            value,
                                    })
                                }
                            >
                                <Select.Trigger
                                    style={{
                                        width: "100%",
                                    }}
                                >
                                    <Select.Value placeholder="Choose a beneficiary" />
                                </Select.Trigger>

                                <Select.Content>
                                    <Select.ScrollUpButton />
                                    <Select.Viewport>
                                        {BENEFICIARIES.map(
                                            (
                                                beneficiary,
                                                index,
                                            ) => (
                                                <Select.Item
                                                    key={
                                                        beneficiary.id
                                                    }
                                                    index={
                                                        index
                                                    }
                                                    value={
                                                        beneficiary.id
                                                    }
                                                >
                                                    <Select.ItemText>
                                                        {
                                                            beneficiary.name
                                                        }
                                                    </Select.ItemText>
                                                </Select.Item>
                                            ),
                                        )}
                                    </Select.Viewport>
                                    <Select.ScrollDownButton />
                                </Select.Content>
                            </Select>

                            {selectedBeneficiary && (
                                <YStack gap="$2">
                                    <Text>
                                        Age:{" "}
                                        {
                                            selectedBeneficiary.age
                                        }
                                    </Text>
                                    <Text>
                                        Gender:{" "}
                                        {
                                            selectedBeneficiary.gender
                                        }
                                    </Text>
                                    <Text>
                                        Medical
                                        Conditions:{" "}
                                        {
                                            selectedBeneficiary.medicalConditions
                                        }
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
