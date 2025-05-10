import { Card, H3, Input, Label, Switch, Text, XStack, YStack } from "tamagui";
import { IFamilyMember } from "../../../../../user.schema";

interface Props {
    data: Partial<IFamilyMember>;
    onChange: (key: keyof IFamilyMember, value: any) => void;
}

const RelationSection = ({ data, onChange }: Props) => {
    return (
        <Card>
            <Card.Header padded>
                <H3>Relationship Information</H3>
            </Card.Header>
            <Card.Footer padded>
                <YStack space="$4">
                    <YStack>
                        <Label htmlFor="relation_to_beneficiary">Relation to Beneficiary</Label>
                        <Input
                            id="relation_to_beneficiary"
                            value={data.relation_to_beneficiary}
                            onChangeText={(value) => onChange("relation_to_beneficiary", value)}
                        />
                    </YStack>
                    <XStack alignItems="center" space="$4">
                        <Label htmlFor="is_primary_caregiver">Primary Caregiver</Label>
                        <Switch
                            id="is_primary_caregiver"
                            checked={data.is_primary_caregiver}
                            onCheckedChange={(value) => onChange("is_primary_caregiver", value)}
                        />
                    </XStack>
                </YStack>
            </Card.Footer>
        </Card>
    );
};

export default RelationSection;
