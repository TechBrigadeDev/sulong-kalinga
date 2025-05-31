import { Card, H3, YStack, Input, XStack, Select } from "tamagui";
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
                        <Input
                            id="emergency_contact_name"
                            value={data.emergency_contact_name}
                            onChangeText={(value) => onChange("emergency_contact_name", value)}
                            placeholder="Contact Name"
                            autoCapitalize="words"
                        />
                    </YStack>

                    <XStack space="$4">
                        <YStack flex={1}>
                            <Select
                                id="emergency_contact_relation"
                                value={data.emergency_contact_relation}
                                onValueChange={(value) => onChange("emergency_contact_relation", value)}
                            >
                                <Select.Trigger>
                                    <Select.Value placeholder="Select Relation" />
                                </Select.Trigger>
                                
                                <Select.Content>
                                    <Select.ScrollUpButton />
                                    <Select.Viewport>
                                        <Select.Group>
                                            {RELATION_OPTIONS.map((option, i) => (
                                                <Select.Item index={i} key={option.value} value={option.value}>
                                                    <Select.ItemText>{option.label}</Select.ItemText>
                                                </Select.Item>
                                            ))}
                                        </Select.Group>
                                    </Select.Viewport>
                                    <Select.ScrollDownButton />
                                </Select.Content>
                            </Select>
                        </YStack>
                    </XStack>

                    <YStack>
                        <Input
                            id="emergency_contact_mobile"
                            value={data.emergency_contact_mobile}
                            onChangeText={(value) => onChange("emergency_contact_mobile", value)}
                            placeholder="Mobile Number"
                            keyboardType="phone-pad"
                        />
                    </YStack>

                    <YStack>
                        <Input
                            id="emergency_contact_email"
                            value={data.emergency_contact_email}
                            onChangeText={(value) => onChange("emergency_contact_email", value)}
                            placeholder="Email Address"
                            keyboardType="email-address"
                            autoCapitalize="none"
                        />
                    </YStack>

                    <YStack>
                        <Input
                            id="emergency_procedure"
                            value={data.emergency_procedure}
                            onChangeText={(value) => onChange("emergency_procedure", value)}
                            placeholder="Emergency Procedures"
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
