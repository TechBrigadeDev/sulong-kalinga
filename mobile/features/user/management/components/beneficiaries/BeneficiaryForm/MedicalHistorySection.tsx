import { Card, H3, Input, Label, Select, YStack } from "tamagui";
import { IBeneficiary } from "../../../user.schema";

interface Props {
    data: Partial<IBeneficiary>;
    onChange: (field: keyof IBeneficiary, value: any) => void;
}

const MedicalHistorySection = ({ data, onChange }: Props) => {
    return (
        <Card elevate>
            <Card.Header padded>
                <H3>Medical History</H3>
            </Card.Header>
            <Card.Footer padded>
                <YStack space="$4">
                    <YStack>
                        <Label htmlFor="medical_conditions">Medical Conditions</Label>
                        <Input
                            id="medical_conditions" 
                            placeholder="List all medical conditions"
                            multiline
                            numberOfLines={3}
                            textAlignVertical="top"
                        />
                    </YStack>

                    <YStack>
                        <Label htmlFor="medications">Medications</Label>
                        <Input
                            id="medications"
                            placeholder="List all medications"
                            multiline
                            numberOfLines={3}
                            textAlignVertical="top"
                        />
                    </YStack>

                    <YStack>
                        <Label htmlFor="allergies">Allergies</Label>
                        <Input
                            id="allergies"
                            placeholder="List all allergies"
                            multiline
                            numberOfLines={3}
                            textAlignVertical="top"
                        />
                    </YStack>

                    <YStack>
                        <Label htmlFor="immunizations">Immunizations</Label>
                        <Input
                            id="immunizations"
                            placeholder="List all immunizations"
                            multiline
                            numberOfLines={3}
                            textAlignVertical="top"
                        />
                    </YStack>

                    <YStack>
                        <Label htmlFor="category_id">Category *</Label>
                        <Select
                            id="category_id"
                            value={data.category_id?.toString()}
                            onValueChange={(value) => onChange("category_id", parseInt(value, 10))}
                        >
                            <Select.Trigger>
                                <Select.Value placeholder="Select category" />
                            </Select.Trigger>

                            <Select.Content>
                                <Select.ScrollUpButton />
                                <Select.Viewport>
                                    <Select.Item value="1">
                                        <Select.ItemText>Dementia</Select.ItemText>
                                    </Select.Item>
                                    <Select.Item value="2">
                                        <Select.ItemText>Cancer</Select.ItemText>
                                    </Select.Item>
                                    <Select.Item value="3">
                                        <Select.ItemText>Stroke</Select.ItemText>
                                    </Select.Item>
                                </Select.Viewport>
                                <Select.ScrollDownButton />
                            </Select.Content>
                        </Select>
                    </YStack>
                </YStack>
            </Card.Footer>
        </Card>
    );
};

export default MedicalHistorySection;
