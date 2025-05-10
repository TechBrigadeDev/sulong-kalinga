import { Card, H3, Input, Label, Select, XStack, YStack } from "tamagui";
import { IBeneficiary } from "../../../user.schema";

interface Props {
    data: Partial<IBeneficiary>;
    onChange: (field: keyof IBeneficiary, value: any) => void;
}

const RELATION_OPTIONS = [
    { label: "Spouse", value: "spouse" },
    { label: "Parent", value: "parent" },
    { label: "Child", value: "child" },
    { label: "Sibling", value: "sibling" },
    { label: "Other Relative", value: "other_relative" },
    { label: "Friend", value: "friend" },
    { label: "Other", value: "other" }
];

const EmergencyContactSection = ({ data, onChange }: Props) => {
    return (
        <Card elevate>
            <Card.Header padded>
                <H3>Emergency Contact</H3>
            </Card.Header>
            <Card.Footer padded>
                <YStack space="$4">
                    <YStack>
                        <Label htmlFor="emergency_contact_name">Contact Name *</Label>
                        <Input
                            id="emergency_contact_name"
                            value={data.emergency_contact_name}
                            onChangeText={(value) => onChange("emergency_contact_name", value)}
                            placeholder="Enter contact name"
                        />
                    </YStack>

                    <XStack space="$4">
                        <YStack flex={1}>
                            <Label htmlFor="emergency_contact_relation">Relation *</Label>
                            <Select
                                id="emergency_contact_relation"
                                value={data.emergency_contact_relation}
                                onValueChange={(value) => onChange("emergency_contact_relation", value)}
                            >
                                <Select.Trigger>
                                    <Select.Value placeholder="Select relation" />
                                </Select.Trigger>

                                <Select.Content>
                                    <Select.ScrollUpButton />
                                    <Select.Viewport>
                                        {RELATION_OPTIONS.map((option) => (
                                            <Select.Item 
                                                key={option.value} 
                                                value={option.value}
                                            >
                                                <Select.ItemText>
                                                    {option.label}
                                                </Select.ItemText>
                                            </Select.Item>
                                        ))}
                                    </Select.Viewport>
                                    <Select.ScrollDownButton />
                                </Select.Content>
                            </Select>
                        </YStack>

                        <YStack flex={1}>
                            <Label htmlFor="emergency_contact_mobile">Mobile *</Label>
                            <Input
                                id="emergency_contact_mobile"
                                value={data.emergency_contact_mobile}
                                onChangeText={(value) => onChange("emergency_contact_mobile", value)}
                                placeholder="+63"
                            />
                        </YStack>
                    </XStack>

                    <YStack>
                        <Label htmlFor="emergency_contact_email">Email</Label>
                        <Input
                            id="emergency_contact_email"
                            value={data.emergency_contact_email}
                            onChangeText={(value) => onChange("emergency_contact_email", value)}
                            placeholder="Enter email address"
                            keyboardType="email-address"
                            autoCapitalize="none"
                        />
                    </YStack>

                    <YStack>
                        <Label htmlFor="emergency_procedure">Emergency Procedures *</Label>
                        <Input
                            id="emergency_procedure"
                            value={data.emergency_procedure}
                            onChangeText={(value) => onChange("emergency_procedure", value)}
                            placeholder="Enter emergency procedures"
                            multiline
                            numberOfLines={4}
                            textAlignVertical="top"
                        />
                    </YStack>
                </YStack>
            </Card.Footer>
        </Card>
    );
};

export default EmergencyContactSection;
