import { BeneficiaryFormValues } from "features/user-management/components/beneficiaries/BeneficiaryForm/schema";
import {
    Controller,
    useFormContext,
} from "react-hook-form";
import {
    Card,
    H3,
    Input,
    Label,
    Text,
    YStack,
} from "tamagui";

export const CognitiveFunctionSection = () => {
    return (
        <YStack gap="$4">
            <Card elevate>
                <Card.Header padded>
                    <H3>Mobility</H3>
                </Card.Header>
                <YStack p="$4" gap="$4">
                    <WalkingAbility />
                    <AssistiveDevices />
                    <TransportationNeeds />
                </YStack>
            </Card>

            <Card elevate>
                <Card.Header padded>
                    <H3>Cognitive Function</H3>
                </Card.Header>
                <YStack p="$4" gap="$4">
                    <Memory />
                    <ThinkingSkills />
                    <Orientation />
                    <Behavior />
                </YStack>
            </Card>

            <Card elevate>
                <Card.Header padded>
                    <H3>Emotional Well-being</H3>
                </Card.Header>
                <YStack p="$4" gap="$4">
                    <Mood />
                    <SocialInteractions />
                    <EmotionalSupportNeed />
                </YStack>
            </Card>
        </YStack>
    );
};

const WalkingAbility = () => {
    const { control } =
        useFormContext<BeneficiaryFormValues>();
    return (
        <Controller
            control={control}
            name="walking_ability"
            render={({ field, fieldState }) => (
                <YStack gap="$2">
                    <Label fontWeight="600">
                        Walking Ability
                    </Label>
                    <Input
                        size="$4"
                        value={field.value || ""}
                        onChangeText={
                            field.onChange
                        }
                        onBlur={field.onBlur}
                        placeholder="Enter details about walking ability"
                        multiline
                        numberOfLines={3}
                        textAlignVertical="top"
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

const AssistiveDevices = () => {
    const { control } =
        useFormContext<BeneficiaryFormValues>();
    return (
        <Controller
            control={control}
            name="assistive_devices"
            render={({ field, fieldState }) => (
                <YStack gap="$2">
                    <Label fontWeight="600">
                        Assistive Devices
                    </Label>
                    <Input
                        size="$4"
                        value={field.value || ""}
                        onChangeText={
                            field.onChange
                        }
                        onBlur={field.onBlur}
                        placeholder="Enter details about assistive devices"
                        multiline
                        numberOfLines={3}
                        textAlignVertical="top"
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

const TransportationNeeds = () => {
    const { control } =
        useFormContext<BeneficiaryFormValues>();
    return (
        <Controller
            control={control}
            name="transportation_needs"
            render={({ field, fieldState }) => (
                <YStack gap="$2">
                    <Label fontWeight="600">
                        Transportation Needs
                    </Label>
                    <Input
                        size="$4"
                        value={field.value || ""}
                        onChangeText={
                            field.onChange
                        }
                        onBlur={field.onBlur}
                        placeholder="Enter details about transportation needs"
                        multiline
                        numberOfLines={3}
                        textAlignVertical="top"
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

const Memory = () => {
    const { control } =
        useFormContext<BeneficiaryFormValues>();
    return (
        <Controller
            control={control}
            name="memory"
            render={({ field, fieldState }) => (
                <YStack gap="$2">
                    <Label fontWeight="600">
                        Memory
                    </Label>
                    <Input
                        size="$4"
                        value={field.value || ""}
                        onChangeText={
                            field.onChange
                        }
                        onBlur={field.onBlur}
                        placeholder="Enter details about memory"
                        multiline
                        numberOfLines={3}
                        textAlignVertical="top"
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

const ThinkingSkills = () => {
    const { control } =
        useFormContext<BeneficiaryFormValues>();
    return (
        <Controller
            control={control}
            name="thinking_skills"
            render={({ field, fieldState }) => (
                <YStack gap="$2">
                    <Label fontWeight="600">
                        Thinking Skills
                    </Label>
                    <Input
                        size="$4"
                        value={field.value || ""}
                        onChangeText={
                            field.onChange
                        }
                        onBlur={field.onBlur}
                        placeholder="Enter details about thinking skills"
                        multiline
                        numberOfLines={3}
                        textAlignVertical="top"
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

const Orientation = () => {
    const { control } =
        useFormContext<BeneficiaryFormValues>();
    return (
        <Controller
            control={control}
            name="orientation"
            render={({ field, fieldState }) => (
                <YStack gap="$2">
                    <Label fontWeight="600">
                        Orientation
                    </Label>
                    <Input
                        size="$4"
                        value={field.value || ""}
                        onChangeText={
                            field.onChange
                        }
                        onBlur={field.onBlur}
                        placeholder="Enter details about orientation"
                        multiline
                        numberOfLines={3}
                        textAlignVertical="top"
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

const Behavior = () => {
    const { control } =
        useFormContext<BeneficiaryFormValues>();
    return (
        <Controller
            control={control}
            name="behavior"
            render={({ field, fieldState }) => (
                <YStack gap="$2">
                    <Label fontWeight="600">
                        Behavior
                    </Label>
                    <Input
                        size="$4"
                        value={field.value || ""}
                        onChangeText={
                            field.onChange
                        }
                        onBlur={field.onBlur}
                        placeholder="Enter details about behavior"
                        multiline
                        numberOfLines={3}
                        textAlignVertical="top"
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

const Mood = () => {
    const { control } =
        useFormContext<BeneficiaryFormValues>();
    return (
        <Controller
            control={control}
            name="mood"
            render={({ field, fieldState }) => (
                <YStack gap="$2">
                    <Label fontWeight="600">
                        Mood
                    </Label>
                    <Input
                        size="$4"
                        value={field.value || ""}
                        onChangeText={
                            field.onChange
                        }
                        onBlur={field.onBlur}
                        placeholder="Enter details about mood"
                        multiline
                        numberOfLines={3}
                        textAlignVertical="top"
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

const SocialInteractions = () => {
    const { control } =
        useFormContext<BeneficiaryFormValues>();
    return (
        <Controller
            control={control}
            name="social_interactions"
            render={({ field, fieldState }) => (
                <YStack gap="$2">
                    <Label fontWeight="600">
                        Social Interactions
                    </Label>
                    <Input
                        size="$4"
                        value={field.value || ""}
                        onChangeText={
                            field.onChange
                        }
                        onBlur={field.onBlur}
                        placeholder="Enter details about social interactions"
                        multiline
                        numberOfLines={3}
                        textAlignVertical="top"
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

const EmotionalSupportNeed = () => {
    const { control } =
        useFormContext<BeneficiaryFormValues>();
    return (
        <Controller
            control={control}
            name="emotional_support_need"
            render={({ field, fieldState }) => (
                <YStack gap="$2">
                    <Label fontWeight="600">
                        Emotional Support Need
                    </Label>
                    <Input
                        size="$4"
                        value={field.value || ""}
                        onChangeText={
                            field.onChange
                        }
                        onBlur={field.onBlur}
                        placeholder="Enter details about emotional support need"
                        multiline
                        numberOfLines={3}
                        textAlignVertical="top"
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
