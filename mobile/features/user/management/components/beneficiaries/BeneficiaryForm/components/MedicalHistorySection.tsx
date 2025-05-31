import { Card, H3, YStack, Input, Text } from "tamagui";
import { IBeneficiary } from "../../../user.schema";

interface Props {
    data?: Partial<IBeneficiary>;
    onChange?: (field: string | number | symbol, value: any) => void;
}

export const MedicalHistorySection = ({ 
    data = {}, 
    onChange = () => {} 
}: Props) => {
    return (
        <Card elevate>
            <Card.Header padded>
                <H3>Medical History</H3>
            </Card.Header>
            <Card.Footer padded>
                <YStack space="$4">
                    <YStack>
                        <Text>Medical Conditions</Text>
                        <Input
                            multiline
                            numberOfLines={3}
                            textAlignVertical="top"
                            value={data.medical_conditions}
                            onChangeText={(value) => onChange("medical_conditions", value)}
                            placeholder="List all medical conditions"
                        />
                        <Text opacity={0.6}>Separate multiple conditions with commas</Text>
                    </YStack>

                    <YStack>
                        <Text>Medications</Text>
                        <Input
                            multiline
                            numberOfLines={3}
                            textAlignVertical="top"
                            value={data.medications}
                            onChangeText={(value) => onChange("medications", value)}
                            placeholder="List all medications"
                        />
                        <Text opacity={0.6}>Separate multiple medications with commas</Text>
                    </YStack>

                    <YStack>
                        <Text>Allergies</Text>
                        <Input
                            multiline
                            numberOfLines={3}
                            textAlignVertical="top"
                            value={data.allergies}
                            onChangeText={(value) => onChange("allergies", value)}
                            placeholder="List all allergies"
                        />
                        <Text opacity={0.6}>Separate multiple allergies with commas</Text>
                    </YStack>

                    <YStack>
                        <Text>Immunizations</Text>
                        <Input
                            multiline
                            numberOfLines={3}
                            textAlignVertical="top"
                            value={data.immunizations}
                            onChangeText={(value) => onChange("immunizations", value)}
                            placeholder="List all immunizations"
                        />
                        <Text opacity={0.6}>Separate multiple immunizations with commas</Text>
                    </YStack>
                </YStack>
            </Card.Footer>
        </Card>
    );
};
