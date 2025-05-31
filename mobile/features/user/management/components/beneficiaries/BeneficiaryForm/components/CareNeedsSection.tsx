import { Card, H3, Input, Label, Text, YStack } from "tamagui";
import { IBeneficiary } from "../../../user.schema";

interface Props {
    data: Partial<IBeneficiary>;
    onChange: (field: keyof IBeneficiary, value: any) => void;
}

const CARE_NEEDS = [
    "Mobility",
    "Cognitive / Communication",
    "Self-sustainability",
    "Disease / Therapy Handling",
    "Daily Life / Social Contact",
    "Outdoor Activities",
    "Household Keeping"
];

const CareNeedsSection = ({ data, onChange }: Props) => {
    return (
        <Card elevate>
            <Card.Header padded>
                <H3>Care Needs *</H3>
            </Card.Header>
            <Card.Footer padded>
                <YStack space="$4">
                    {CARE_NEEDS.map((need, index) => (
                        <Card key={index} bordered>
                            <Card.Header padded>
                                <Text>{need}</Text>
                            </Card.Header>
                            <Card.Footer padded>
                                <YStack space="$2">
                                    <YStack>
                                        <Label>Frequency</Label>
                                        <Input placeholder="Enter frequency" />
                                    </YStack>
                                    <YStack>
                                        <Label>Assistance Required</Label>
                                        <Input 
                                            placeholder="Describe assistance required"
                                            multiline
                                            numberOfLines={2}
                                            textAlignVertical="top"
                                        />
                                    </YStack>
                                </YStack>
                            </Card.Footer>
                        </Card>
                    ))}
                </YStack>
            </Card.Footer>
        </Card>
    );
};

export default CareNeedsSection;
