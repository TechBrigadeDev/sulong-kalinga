import { Button, Card, H3, Input, Label, Text, YStack } from "tamagui";
import { IBeneficiary } from "../../../user.schema";
import { useState } from "react";

interface Props {
    data: Partial<IBeneficiary>;
    onChange: (field: keyof IBeneficiary, value: any) => void;
}

interface Medication {
    name: string;
    dosage: string;
    frequency: string;
    instructions: string;
}

const MedicationSection = ({ data, onChange }: Props) => {
    const [medications, setMedications] = useState<Medication[]>([]);
    const [currentMed, setCurrentMed] = useState<Medication>({
        name: "",
        dosage: "",
        frequency: "",
        instructions: ""
    });

    const addMedication = () => {
        if (currentMed.name) {
            setMedications([...medications, currentMed]);
            setCurrentMed({
                name: "",
                dosage: "",
                frequency: "",
                instructions: ""
            });
        }
    };

    const deleteMedication = (index: number) => {
        const newMeds = medications.filter((_, i) => i !== index);
        setMedications(newMeds);
    };

    return (
        <Card elevate>
            <Card.Header padded>
                <H3>Medication Management</H3>
            </Card.Header>
            <Card.Footer padded>
                <YStack space="$4">
                    <Card bordered>
                        <Card.Header padded>
                            <Text>Add New Medication</Text>
                        </Card.Header>
                        <Card.Footer padded>
                            <YStack space="$3">
                                <YStack>
                                    <Label htmlFor="med_name">Medication Name</Label>
                                    <Input
                                        id="med_name"
                                        value={currentMed.name}
                                        onChangeText={(value) => 
                                            setCurrentMed(prev => ({ ...prev, name: value }))
                                        }
                                        placeholder="Enter medication name"
                                    />
                                </YStack>

                                <YStack>
                                    <Label htmlFor="dosage">Dosage</Label>
                                    <Input
                                        id="dosage"
                                        value={currentMed.dosage}
                                        onChangeText={(value) => 
                                            setCurrentMed(prev => ({ ...prev, dosage: value }))
                                        }
                                        placeholder="Enter dosage"
                                    />
                                </YStack>

                                <YStack>
                                    <Label htmlFor="frequency">Frequency</Label>
                                    <Input
                                        id="frequency"
                                        value={currentMed.frequency}
                                        onChangeText={(value) => 
                                            setCurrentMed(prev => ({ ...prev, frequency: value }))
                                        }
                                        placeholder="Enter frequency"
                                    />
                                </YStack>

                                <YStack>
                                    <Label htmlFor="instructions">Administration Instructions</Label>
                                    <Input
                                        id="instructions"
                                        value={currentMed.instructions}
                                        onChangeText={(value) => 
                                            setCurrentMed(prev => ({ ...prev, instructions: value }))
                                        }
                                        placeholder="Enter administration instructions"
                                        multiline
                                        numberOfLines={2}
                                    />
                                </YStack>

                                <Button onPress={addMedication}>
                                    Add Medication
                                </Button>
                            </YStack>
                        </Card.Footer>
                    </Card>

                    {medications.map((med, index) => (
                        <Card key={index} bordered>
                            <Card.Header padded>
                                <Text fontSize="$5">{med.name}</Text>
                            </Card.Header>
                            <Card.Footer padded>
                                <YStack space="$2">
                                    <Text>Dosage: {med.dosage}</Text>
                                    <Text>Frequency: {med.frequency}</Text>
                                    <Text>Instructions: {med.instructions}</Text>
                                    <Button 
                                        onPress={() => deleteMedication(index)}
                                        theme="red"
                                    >
                                        Delete
                                    </Button>
                                </YStack>
                            </Card.Footer>
                        </Card>
                    ))}
                </YStack>
            </Card.Footer>
        </Card>
    );
};

export default MedicationSection;
