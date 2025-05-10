import { Card, H3, Input, Label, Select, YStack } from "tamagui";
import { IBeneficiary } from "../../../user.schema";
import { Sheet } from "@tamagui/sheet";

interface Props {
    data: Partial<IBeneficiary>;
    onChange: (field: keyof IBeneficiary, value: any) => void;
}

const CIVIL_STATUS_OPTIONS = [
    { label: "Single", value: "single" },
    { label: "Married", value: "married" },
    { label: "Widowed", value: "widowed" },
    { label: "Divorced", value: "divorced" },
    { label: "Separated", value: "separated" }
];

const GENDER_OPTIONS = [
    { label: "Male", value: "male" },
    { label: "Female", value: "female" }
];

const PersonalDetailsSection = ({ data, onChange }: Props) => {
    return (
        <Card elevate>
            <Card.Header padded>
                <H3>Personal Details</H3>
            </Card.Header>
            <Card.Footer padded>
                <YStack space="$4">
                    <YStack space="$4">
                        <YStack space="$2">
                            <Label htmlFor="first_name">First Name *</Label>
                            <Input
                                id="first_name"
                                value={data.first_name}
                                onChangeText={(value) => onChange("first_name", value)}
                                placeholder="Enter first name"
                            />
                        </YStack>
                        <YStack space="$2">
                            <Label htmlFor="last_name">Last Name *</Label>
                            <Input
                                id="last_name"
                                value={data.last_name}
                                onChangeText={(value) => onChange("last_name", value)}
                                placeholder="Enter last name"
                            />
                        </YStack>
                    </YStack>

                    <YStack space="$4">
                        <YStack space="$2">
                            <Label htmlFor="civil_status">Civil Status *</Label>
                            <Select
                                id="civil_status"
                                value={data.civil_status}
                                onValueChange={(value) => onChange("civil_status", value)}
                                native
                            >
                                <Select.Trigger>
                                    <Select.Value placeholder="Select civil status" />
                                </Select.Trigger>
                                <Select.Content>
                                    <Select.ScrollUpButton />
                                    <Select.Viewport>
                                        {CIVIL_STATUS_OPTIONS.map((option, index) => (
                                            <Select.Item 
                                                key={option.value} 
                                                value={option.value}
                                                index={index}
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

                        <YStack space="$2">
                            <Label htmlFor="gender">Gender *</Label>
                            <Select
                                id="gender"
                                value={data.gender}
                                onValueChange={(value) => onChange("gender", value)}
                                native
                            >
                                <Select.Trigger>
                                    <Select.Value placeholder="Select gender" />
                                </Select.Trigger>
                                <Select.Content>
                                    <Select.ScrollUpButton />
                                    <Select.Viewport>
                                        {GENDER_OPTIONS.map((option, index) => (
                                            <Select.Item 
                                                key={option.value} 
                                                value={option.value}
                                                index={index}
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
                    </YStack>

                    <YStack space="$4">
                        <YStack space="$2">
                            <Label htmlFor="birthday">Birthday *</Label>
                            <Input
                                id="birthday"
                                value={data.birthday}
                                onChangeText={(value) => onChange("birthday", value)}
                                placeholder="YYYY-MM-DD"
                            />
                        </YStack>
                        <YStack space="$2">
                            <Label htmlFor="primary_caregiver">Primary Caregiver</Label>
                            <Input
                                id="primary_caregiver"
                                value={data.primary_caregiver}
                                onChangeText={(value) => onChange("primary_caregiver", value)}
                                placeholder="Enter primary caregiver"
                            />
                        </YStack>
                    </YStack>

                    <YStack space="$4">
                        <YStack space="$2">
                            <Label htmlFor="mobile">Mobile Number</Label>
                            <Input
                                id="mobile"
                                value={data.mobile}
                                onChangeText={(value) => onChange("mobile", value)}
                                placeholder="+63"
                            />
                        </YStack>
                        <YStack space="$2">
                            <Label htmlFor="landline">Landline Number</Label>
                            <Input
                                id="landline"
                                value={data.landline}
                                onChangeText={(value) => onChange("landline", value)}
                                placeholder="Enter landline number"
                            />
                        </YStack>
                    </YStack>
                </YStack>
            </Card.Footer>
        </Card>
    );
};

export default PersonalDetailsSection;
