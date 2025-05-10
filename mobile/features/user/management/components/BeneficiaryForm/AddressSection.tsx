import { Card, H3, Input, Label, Select, YStack } from "tamagui";
import { IBeneficiary } from "../../../user.schema";

interface Props {
    data: Partial<IBeneficiary>;
    onChange: (field: keyof IBeneficiary, value: any) => void;
}

const AddressSection = ({ data, onChange }: Props) => {
    return (
        <Card elevate>
            <Card.Header padded>
                <H3>Current Address</H3>
            </Card.Header>
            <Card.Footer padded>
                <YStack space="$4">
                    <YStack>
                        <Label htmlFor="street_address">House No., Street, Subdivision, Barangay, City, Province *</Label>
                        <Input
                            id="street_address"
                            value={data.street_address}
                            onChangeText={(value) => onChange("street_address", value)}
                            placeholder="Enter complete current address"
                            multiline
                            numberOfLines={3}
                        />
                    </YStack>

                    <YStack>
                        <Label htmlFor="municipality_id">Municipality *</Label>
                        <Select
                            id="municipality_id"
                            value={data.municipality_id?.toString()}
                            onValueChange={(value) => onChange("municipality_id", parseInt(value, 10))}
                        >
                            <Select.Trigger>
                                <Select.Value placeholder="Select municipality" />
                            </Select.Trigger>

                            <Select.Content>
                                <Select.ScrollUpButton />
                                <Select.Viewport>
                                    <Select.Item value="1">
                                        <Select.ItemText>Municipality 1</Select.ItemText>
                                    </Select.Item>
                                    <Select.Item value="2">
                                        <Select.ItemText>Municipality 2</Select.ItemText>
                                    </Select.Item>
                                </Select.Viewport>
                                <Select.ScrollDownButton />
                            </Select.Content>
                        </Select>
                    </YStack>

                    <YStack>
                        <Label htmlFor="barangay_id">Barangay *</Label>
                        <Select
                            id="barangay_id"
                            value={data.barangay_id?.toString()}
                            onValueChange={(value) => onChange("barangay_id", parseInt(value, 10))}
                        >
                            <Select.Trigger>
                                <Select.Value placeholder="Select barangay" />
                            </Select.Trigger>

                            <Select.Content>
                                <Select.ScrollUpButton />
                                <Select.Viewport>
                                    <Select.Item value="1">
                                        <Select.ItemText>Barangay 1</Select.ItemText>
                                    </Select.Item>
                                    <Select.Item value="2">
                                        <Select.ItemText>Barangay 2</Select.ItemText>
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

export default AddressSection;
