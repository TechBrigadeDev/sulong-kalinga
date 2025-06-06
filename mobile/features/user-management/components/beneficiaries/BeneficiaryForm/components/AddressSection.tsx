import { Card, H3, Input, Select, Text, XStack, YStack } from "tamagui";

import { IBeneficiary } from "~/features/user-management/management.type";

interface Props {
    data?: Partial<IBeneficiary>;
    onChange?: (field: string | number | symbol, value: any) => void;
}

// TODO: These should come from an API or store
const MUNICIPALITY_OPTIONS = [
    { label: "Municipality 1", value: "1" },
    { label: "Municipality 2", value: "2" },
];

const BARANGAY_OPTIONS = [
    { label: "Barangay 1", value: "1" },
    { label: "Barangay 2", value: "2" },
];

export const AddressSection = ({ data = {}, onChange = () => {} }: Props) => {
    return (
        <Card elevate>
            <Card.Header padded>
                <H3>Current Address</H3>
            </Card.Header>
            <Card.Footer padded>
                <YStack gap="$4">
                    <YStack>
                        <Text>House No., Street, Subdivision, Barangay, City, Province *</Text>
                        <Input
                            value={data.street_address}
                            onChangeText={(value) => onChange("street_address", value)}
                            placeholder="Enter complete current address"
                            multiline
                            numberOfLines={3}
                            textAlignVertical="top"
                        />
                    </YStack>

                    <XStack gap="$4">
                        <YStack flex={1}>
                            <Text>Municipality *</Text>
                            <Select
                                value={data.municipality_id?.toString()}
                                onValueChange={(value) =>
                                    onChange("municipality_id", parseInt(value))
                                }
                            >
                                <Select.Trigger>
                                    <Select.Value placeholder="Select municipality" />
                                </Select.Trigger>

                                <Select.Content>
                                    <Select.ScrollUpButton />
                                    <Select.Viewport>
                                        <Select.Group>
                                            {MUNICIPALITY_OPTIONS.map((option, i) => (
                                                <Select.Item
                                                    index={i}
                                                    key={option.value}
                                                    value={option.value}
                                                >
                                                    <Select.ItemText>
                                                        {option.label}
                                                    </Select.ItemText>
                                                </Select.Item>
                                            ))}
                                        </Select.Group>
                                    </Select.Viewport>
                                    <Select.ScrollDownButton />
                                </Select.Content>
                            </Select>
                        </YStack>

                        <YStack flex={1}>
                            <Text>Barangay *</Text>
                            <Select
                                value={data.barangay_id?.toString()}
                                onValueChange={(value) => onChange("barangay_id", parseInt(value))}
                            >
                                <Select.Trigger>
                                    <Select.Value placeholder="Select barangay" />
                                </Select.Trigger>

                                <Select.Content>
                                    <Select.ScrollUpButton />
                                    <Select.Viewport>
                                        <Select.Group>
                                            {BARANGAY_OPTIONS.map((option, i) => (
                                                <Select.Item
                                                    index={i}
                                                    key={option.value}
                                                    value={option.value}
                                                >
                                                    <Select.ItemText>
                                                        {option.label}
                                                    </Select.ItemText>
                                                </Select.Item>
                                            ))}
                                        </Select.Group>
                                    </Select.Viewport>
                                    <Select.ScrollDownButton />
                                </Select.Content>
                            </Select>
                        </YStack>
                    </XStack>
                </YStack>
            </Card.Footer>
        </Card>
    );
};
