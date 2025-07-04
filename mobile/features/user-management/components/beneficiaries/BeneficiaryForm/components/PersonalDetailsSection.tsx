import { BeneficiaryFormValues } from "features/user-management/components/beneficiaries/BeneficiaryForm/schema";
import {
    Controller,
    useFormContext,
} from "react-hook-form";
import {
    Adapt,
    Card,
    H3,
    Input,
    Label,
    Select,
    Sheet,
    Text,
    XStack,
    YStack,
} from "tamagui";

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

export const PersonalDetailsSection = () => {
    return (
        <Card elevate>
            <Card.Header padded>
                <H3>Personal Details</H3>
            </Card.Header>
            <YStack p="$4">
                <YStack gap="$4">
                    <XStack gap="$4">
                        <FirstName />
                        <LastName />
                    </XStack>

                    <XStack gap="$4">
                        <CivilStatus />
                        <Gender />
                    </XStack>
                    <XStack gap="$4">
                        <Birthday />
                        <PrimaryCaregiver />
                    </XStack>

                    <XStack gap="$4">
                        <Mobile />
                    </XStack>
                </YStack>
            </YStack>
        </Card>
    );
};

const FirstName = () => {
    const { control } =
        useFormContext<BeneficiaryFormValues>();
    return (
        <Controller
            control={control}
            name="first_name"
            render={({ field, fieldState }) => (
                <YStack flex={1} gap="$2">
                    <Label htmlFor="first_name">
                        First Name *
                    </Label>
                    <Input
                        id="first_name"
                        size="$4"
                        value={field.value || ""}
                        onChangeText={
                            field.onChange
                        }
                        onBlur={field.onBlur}
                        placeholder="Enter first name"
                        autoCapitalize="words"
                    />
                    {fieldState.error && (
                        <Text
                            color="$red10"
                            fontSize="$2"
                        >
                            {
                                fieldState.error
                                    .message
                            }
                        </Text>
                    )}
                </YStack>
            )}
        />
    );
};

const LastName = () => {
    const { control } =
        useFormContext<BeneficiaryFormValues>();
    return (
        <Controller
            control={control}
            name="last_name"
            render={({ field, fieldState }) => (
                <YStack flex={1} gap="$2">
                    <Label htmlFor="last_name">
                        Last Name *
                    </Label>
                    <Input
                        id="last_name"
                        size="$4"
                        value={field.value || ""}
                        onChangeText={
                            field.onChange
                        }
                        onBlur={field.onBlur}
                        placeholder="Enter last name"
                        autoCapitalize="words"
                    />
                    {fieldState.error && (
                        <Text
                            color="$red10"
                            fontSize="$2"
                        >
                            {
                                fieldState.error
                                    .message
                            }
                        </Text>
                    )}
                </YStack>
            )}
        />
    );
};

const CivilStatus = () => {
    const { control } =
        useFormContext<BeneficiaryFormValues>();
    return (
        <Controller
            control={control}
            name="civil_status"
            render={({ field, fieldState }) => (
                <YStack flex={1} gap="$2">
                    <Label htmlFor="civil_status">
                        Civil Status
                    </Label>
                    <Select
                        value={field.value || ""}
                        onValueChange={
                            field.onChange
                        }
                    >
                        <Select.Trigger>
                            <Select.Value placeholder="Select status" />
                        </Select.Trigger>
                        <Select.Content>
                            <Select.ScrollUpButton />
                            <Select.Viewport
                                bg="$white10"
                                p="$2"
                                style={{
                                    maxHeight: 300,
                                    minHeight: 200,
                                }}
                            >
                                {CIVIL_STATUS_OPTIONS.map(
                                    (
                                        option,
                                        index,
                                    ) => (
                                        <Select.Item
                                            key={
                                                option.value
                                            }
                                            index={
                                                index
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
                            </Select.Viewport>
                        </Select.Content>
                        <Adapt
                            when="maxMd"
                            platform="touch"
                        >
                            <Sheet>
                                <Sheet.Frame>
                                    <Adapt.Contents />
                                </Sheet.Frame>
                                <Sheet.Overlay bg="transparent" />
                            </Sheet>
                        </Adapt>
                    </Select>
                    {fieldState.error && (
                        <Text
                            color="$red10"
                            fontSize="$2"
                        >
                            {
                                fieldState.error
                                    .message
                            }
                        </Text>
                    )}
                </YStack>
            )}
        />
    );
};

const Gender = () => {
    const { control } =
        useFormContext<BeneficiaryFormValues>();
    return (
        <Controller
            control={control}
            name="gender"
            render={({ field, fieldState }) => (
                <YStack flex={1} gap="$2">
                    <Label htmlFor="gender">
                        Gender *
                    </Label>
                    <Select
                        value={field.value || ""}
                        onValueChange={
                            field.onChange
                        }
                    >
                        <Select.Trigger>
                            <Select.Value placeholder="Select gender" />
                        </Select.Trigger>
                        <Select.Content>
                            <Select.ScrollUpButton />
                            <Select.Viewport
                                bg="$white10"
                                p="$2"
                                style={{
                                    maxHeight: 300,
                                    minHeight: 200,
                                }}
                            >
                                {GENDER_OPTIONS.map(
                                    (
                                        option,
                                        index,
                                    ) => (
                                        <Select.Item
                                            key={
                                                option.value
                                            }
                                            index={
                                                index
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
                            </Select.Viewport>
                            <Adapt
                                when="maxMd"
                                platform="touch"
                            >
                                <Sheet>
                                    <Sheet.Frame>
                                        <Adapt.Contents />
                                    </Sheet.Frame>
                                    <Sheet.Overlay bg="transparent" />
                                </Sheet>
                            </Adapt>
                        </Select.Content>
                    </Select>
                    {fieldState.error && (
                        <Text
                            color="$red10"
                            fontSize="$2"
                        >
                            {
                                fieldState.error
                                    .message
                            }
                        </Text>
                    )}
                </YStack>
            )}
        />
    );
};

const Birthday = () => {
    const { control } =
        useFormContext<BeneficiaryFormValues>();
    return (
        <Controller
            control={control}
            name="birthday"
            render={({ field, fieldState }) => (
                <YStack flex={1} gap="$2">
                    <Label htmlFor="birthday">
                        Birthday *
                    </Label>
                    <Input
                        id="birthday"
                        size="$4"
                        value={field.value || ""}
                        onChangeText={
                            field.onChange
                        }
                        onBlur={field.onBlur}
                        placeholder="YYYY-MM-DD"
                        keyboardType="numeric"
                        maxLength={10}
                        autoComplete="birthdate-full"
                    />
                    <Text
                        opacity={0.6}
                        fontSize="$2"
                    >
                        Format: YYYY-MM-DD (e.g.,
                        1990-01-15)
                    </Text>
                    {fieldState.error && (
                        <Text
                            color="$red10"
                            fontSize="$2"
                        >
                            {
                                fieldState.error
                                    .message
                            }
                        </Text>
                    )}
                </YStack>
            )}
        />
    );
};

const PrimaryCaregiver = () => {
    const { control } =
        useFormContext<BeneficiaryFormValues>();
    return (
        <Controller
            control={control}
            name="primary_caregiver"
            render={({ field, fieldState }) => (
                <YStack flex={1} gap="$2">
                    <Label htmlFor="primary_caregiver">
                        Primary Caregiver
                    </Label>
                    <Input
                        id="primary_caregiver"
                        size="$4"
                        value={field.value || ""}
                        onChangeText={
                            field.onChange
                        }
                        onBlur={field.onBlur}
                        placeholder="Enter caregiver name"
                        autoCapitalize="words"
                    />
                    {fieldState.error && (
                        <Text
                            color="$red10"
                            fontSize="$2"
                        >
                            {
                                fieldState.error
                                    .message
                            }
                        </Text>
                    )}
                </YStack>
            )}
        />
    );
};

const Mobile = () => {
    const { control } =
        useFormContext<BeneficiaryFormValues>();
    return (
        <Controller
            control={control}
            name="mobile"
            render={({ field, fieldState }) => (
                <YStack flex={1} gap="$2">
                    <Label htmlFor="mobile">
                        Mobile Number *
                    </Label>
                    <Input
                        id="mobile"
                        size="$4"
                        value={
                            field.value?.replace(
                                "+63",
                                "",
                            ) || ""
                        }
                        onChangeText={(value) =>
                            field.onChange(
                                `+63${value}`,
                            )
                        }
                        onBlur={field.onBlur}
                        placeholder="Enter mobile number"
                        keyboardType="phone-pad"
                        maxLength={10}
                        autoComplete="tel"
                    />
                    <Text
                        opacity={0.6}
                        fontSize="$2"
                    >
                        Format: 9XXXXXXXXX (will
                        be saved as +639XXXXXXXXX)
                    </Text>
                    {fieldState.error && (
                        <Text
                            color="$red10"
                            fontSize="$2"
                        >
                            {
                                fieldState.error
                                    .message
                            }
                        </Text>
                    )}
                </YStack>
            )}
        />
    );
};
