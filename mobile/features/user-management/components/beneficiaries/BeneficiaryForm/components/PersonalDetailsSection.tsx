import { useState } from "react";
import {
    Card,
    H3,
    Input,
    Select,
    Text,
    XStack,
    YStack,
} from "tamagui";

import { IBeneficiary } from "~/features/user-management/management.type";

interface Props {
    data?: Partial<IBeneficiary>;
    onChange?: (
        field: string | number | symbol,
        value: any,
    ) => void;
}

const CIVIL_STATUS_OPTIONS = [
    { label: "Single", value: "Single" },
    { label: "Married", value: "Married" },
    { label: "Widowed", value: "Widowed" },
    { label: "Divorced", value: "Divorced" },
];

const GENDER_OPTIONS = [
    { label: "Male", value: "Male" },
    { label: "Female", value: "Female" },
    { label: "Other", value: "Other" },
];

export const PersonalDetailsSection = ({
    data = {},
    onChange = () => {},
}: Props) => {
    const [birthday, setBirthday] = useState(
        data.birthday || "",
    );

    return (
        <Card elevate>
            <Card.Header padded>
                <H3>Personal Details</H3>
            </Card.Header>
            <YStack p="$4">
                <YStack gap="$4">
                    <XStack gap="$4">
                        <YStack flex={1} gap="$2">
                            <Text>
                                First Name *
                            </Text>
                            <Input
                                size="$4"
                                value={
                                    data.first_name
                                }
                                onChangeText={(
                                    value,
                                ) =>
                                    onChange(
                                        "first_name",
                                        value,
                                    )
                                }
                                placeholder="Enter first name"
                                autoCapitalize="words"
                            />
                        </YStack>
                        <YStack flex={1} gap="$2">
                            <Text>
                                Last Name *
                            </Text>
                            <Input
                                size="$4"
                                value={
                                    data.last_name
                                }
                                onChangeText={(
                                    value,
                                ) =>
                                    onChange(
                                        "last_name",
                                        value,
                                    )
                                }
                                placeholder="Enter last name"
                                autoCapitalize="words"
                            />
                        </YStack>
                    </XStack>

                    <XStack gap="$4">
                        <YStack flex={1} gap="$2">
                            <Text>
                                Civil Status *
                            </Text>
                            <Select
                                size="$4"
                                value={
                                    data.civil_status
                                }
                                onValueChange={(
                                    value,
                                ) =>
                                    onChange(
                                        "civil_status",
                                        value,
                                    )
                                }
                            >
                                <Select.Trigger>
                                    <Select.Value placeholder="Select civil status" />
                                </Select.Trigger>
                                <Select.Content>
                                    <Select.ScrollUpButton />
                                    <Select.Viewport>
                                        <Select.Group>
                                            {CIVIL_STATUS_OPTIONS.map(
                                                (
                                                    option,
                                                    i,
                                                ) => (
                                                    <Select.Item
                                                        index={
                                                            i
                                                        }
                                                        key={
                                                            option.value
                                                        }
                                                        value={
                                                            option.value
                                                        }
                                                    >
                                                        <Select.ItemText>
                                                            {
                                                                option.label
                                                            }
                                                        </Select.ItemText>
                                                    </Select.Item>
                                                ),
                                            )}
                                        </Select.Group>
                                    </Select.Viewport>
                                    <Select.ScrollDownButton />
                                </Select.Content>
                            </Select>
                        </YStack>
                        <YStack flex={1} gap="$2">
                            <Text>Gender *</Text>
                            <Select
                                size="$4"
                                value={
                                    data.gender
                                }
                                onValueChange={(
                                    value,
                                ) =>
                                    onChange(
                                        "gender",
                                        value,
                                    )
                                }
                            >
                                <Select.Trigger>
                                    <Select.Value placeholder="Select gender" />
                                </Select.Trigger>
                                <Select.Content>
                                    <Select.ScrollUpButton />
                                    <Select.Viewport>
                                        <Select.Group>
                                            {GENDER_OPTIONS.map(
                                                (
                                                    option,
                                                    i,
                                                ) => (
                                                    <Select.Item
                                                        index={
                                                            i
                                                        }
                                                        key={
                                                            option.value
                                                        }
                                                        value={
                                                            option.value
                                                        }
                                                    >
                                                        <Select.ItemText>
                                                            {
                                                                option.label
                                                            }
                                                        </Select.ItemText>
                                                    </Select.Item>
                                                ),
                                            )}
                                        </Select.Group>
                                    </Select.Viewport>
                                    <Select.ScrollDownButton />
                                </Select.Content>
                            </Select>
                        </YStack>
                    </XStack>

                    <XStack gap="$4">
                        <YStack flex={1} gap="$2">
                            <Text>
                                Birthday *
                            </Text>
                            <Input
                                size="$4"
                                value={birthday}
                                onChangeText={(
                                    value,
                                ) => {
                                    setBirthday(
                                        value,
                                    );
                                    onChange(
                                        "birthday",
                                        value,
                                    );
                                }}
                                placeholder="YYYY-MM-DD"
                            />
                        </YStack>
                        <YStack flex={1} gap="$2">
                            <Text>
                                Primary Caregiver
                            </Text>
                            <Input
                                size="$4"
                                value={
                                    data.primary_caregiver
                                }
                                onChangeText={(
                                    value,
                                ) =>
                                    onChange(
                                        "primary_caregiver",
                                        value,
                                    )
                                }
                                placeholder="Enter Primary Caregiver name"
                                autoCapitalize="words"
                            />
                        </YStack>
                    </XStack>

                    <XStack gap="$4">
                        <YStack flex={1} gap="$2">
                            <Text>
                                Mobile Number *
                            </Text>
                            <Input
                                size="$4"
                                value={data.mobile?.replace(
                                    "+63",
                                    "",
                                )}
                                onChangeText={(
                                    value,
                                ) =>
                                    onChange(
                                        "mobile",
                                        `+63${value}`,
                                    )
                                }
                                placeholder="Enter mobile number"
                                keyboardType="phone-pad"
                            />
                        </YStack>
                    </XStack>
                </YStack>
            </YStack>
        </Card>
    );
};
