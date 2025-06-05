import { useRouter } from "expo-router";
import { TouchableOpacity } from "react-native";
import { Button, Card, H3, Input, Label, Switch, Text, XStack, YStack } from "tamagui";

interface IFamilyMember {
    relation_to_beneficiary?: string;
    is_primary_caregiver?: boolean;
}

interface Props {
    data: Partial<IFamilyMember>;
    onChange: (key: keyof IFamilyMember, value: any) => void;
}

const RelationSection = ({ data, onChange }: Props) => {
    const router = useRouter();

    const onBeneficiarySelect = () => {
        console.log("Navigating to Select Beneficiary Modal");
        router.push("/(modals)/select-beneficiary");
    }
    return (
        <Card elevate bordered>
            <Card.Header padded>
                <H3>Relationship Information</H3>
            </Card.Header>
            <Card.Footer padded>
                <YStack gap="$4">
                    <YStack gap="$2">
                        <Label htmlFor="beneficiary">Beneficiary</Label>
                            <Button
                                onPress={onBeneficiarySelect}
                                size="$4"
                            >
                                Select Beneficiary
                            </Button>
                    </YStack>
                    <YStack gap="$2">
                        <Label htmlFor="relation_to_beneficiary">Relation to Beneficiary</Label>
                        <Input
                            id="relation_to_beneficiary"
                            value={data.relation_to_beneficiary}
                            onChangeText={(value) => onChange("relation_to_beneficiary", value)}
                            placeholder="e.g. Parent, Sibling, Child"
                            autoCapitalize="words"
                        />
                    </YStack>
                    <YStack gap="$2">
                        <XStack gap="$4">
                            <Switch
                                id="is_primary_caregiver"
                                checked={data.is_primary_caregiver}
                                onCheckedChange={(value) => onChange("is_primary_caregiver", value)}
                                size="$4"
                            >
                                <Switch.Thumb animation="quick" />
                            </Switch>
                            <Label htmlFor="is_primary_caregiver">Primary Caregiver</Label>
                        </XStack>
                        <Text opacity={0.65}>
                            Toggle this if this family member is the primary caregiver
                        </Text>
                    </YStack>
                </YStack>
            </Card.Footer>
        </Card>
    );
};

export default RelationSection;
