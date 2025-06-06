import { Ionicons } from "@expo/vector-icons";
import { useState } from "react";
import { Button, Card, H3, Input, Text, XStack, YStack } from "tamagui";

import { IBeneficiary } from "~/features/user-management/management.type";

interface Props {
    data: Partial<IBeneficiary>;
    onChange: (field: string | number | symbol, value: any) => void;
}

interface Medication {
    name: string;
    dosage: string;
    frequency: string;
    instructions: string;
}

export const MedicationSection = ({ data, onChange }: Props) => {
    const [medications, setMedications] = useState<Medication[]>(data.medications_list || []);
    const [currentMedication, setCurrentMedication] = useState<Medication>({
        name: "",
        dosage: "",
        frequency: "",
        instructions: "",
    });

    const handleAddMedication = () => {
        if (currentMedication.name && currentMedication.dosage) {
            const newMedications = [...medications, { ...currentMedication }];
            setMedications(newMedications);
            onChange("medications_list", newMedications);
            setCurrentMedication({
                name: "",
                dosage: "",
                frequency: "",
                instructions: "",
            });
        }
    };

    const handleRemoveMedication = (index: number) => {
        const newMedications = medications.filter((_, i) => i !== index);
        setMedications(newMedications);
        onChange("medications_list", newMedications);
    };

    return (
        <Card elevate>
            <Card.Header padded>
                <H3>Medication Management</H3>
            </Card.Header>
            <Card.Footer padded>
                <YStack gap="$4">
                    {medications.map((med, index) => (
                        <Card key={index} bordered>
                            <Card.Footer padded>
                                <XStack space justifyContent="space-between" alignItems="center">
                                    <YStack gap="$2" flex={1}>
                                        <Text fontWeight="bold">{med.name}</Text>
                                        <Text>Dosage: {med.dosage}</Text>
                                        <Text>Frequency: {med.frequency}</Text>
                                        {med.instructions && (
                                            <Text>Instructions: {med.instructions}</Text>
                                        )}
                                    </YStack>
                                    <Button
                                        theme="red"
                                        size="$3"
                                        onPress={() => handleRemoveMedication(index)}
                                    >
                                        <Ionicons name="trash-outline" size={20} color="white" />
                                    </Button>
                                </XStack>
                            </Card.Footer>
                        </Card>
                    ))}

                    <Card bordered theme="gray">
                        <Card.Footer padded>
                            <YStack gap="$4">
                                <Input
                                    placeholder="Medication Name"
                                    value={currentMedication.name}
                                    onChangeText={(value) =>
                                        setCurrentMedication((prev) => ({ ...prev, name: value }))
                                    }
                                />
                                <Input
                                    placeholder="Dosage"
                                    value={currentMedication.dosage}
                                    onChangeText={(value) =>
                                        setCurrentMedication((prev) => ({ ...prev, dosage: value }))
                                    }
                                />
                                <Input
                                    placeholder="Frequency"
                                    value={currentMedication.frequency}
                                    onChangeText={(value) =>
                                        setCurrentMedication((prev) => ({
                                            ...prev,
                                            frequency: value,
                                        }))
                                    }
                                />
                                <Input
                                    placeholder="Administration Instructions"
                                    value={currentMedication.instructions}
                                    onChangeText={(value) =>
                                        setCurrentMedication((prev) => ({
                                            ...prev,
                                            instructions: value,
                                        }))
                                    }
                                    multiline
                                    numberOfLines={2}
                                    textAlignVertical="top"
                                />
                                <Button
                                    theme="blue"
                                    onPress={handleAddMedication}
                                    icon={<Ionicons name="add-outline" size={20} color="white" />}
                                >
                                    Add Medication
                                </Button>
                            </YStack>
                        </Card.Footer>
                    </Card>
                </YStack>
            </Card.Footer>
        </Card>
    );
};
