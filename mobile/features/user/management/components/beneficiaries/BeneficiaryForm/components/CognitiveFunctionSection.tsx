import { Card, H3, YStack, Input, XStack, Text } from "tamagui";
import { IBeneficiary } from "~/features/user/management/management.type";

interface Props {
    data?: Partial<IBeneficiary>;
    onChange?: (field: string | number | symbol, value: any) => void;
}

interface Field {
    label: string;
    field: keyof IBeneficiary;
    placeholder: string;
}

const MOBILITY_FIELDS: Field[] = [
    {
        label: "Walking Ability",
        field: "walking_ability",
        placeholder: "Enter details about walking ability"
    },
    {
        label: "Assistive Devices",
        field: "assistive_devices",
        placeholder: "Enter details about assistive devices"
    },
    {
        label: "Transportation Needs",
        field: "transportation_needs",
        placeholder: "Enter details about transportation needs"
    }
];

const COGNITIVE_FIELDS: Field[] = [
    {
        label: "Memory",
        field: "memory",
        placeholder: "Enter details about memory"
    },
    {
        label: "Thinking Skills",
        field: "thinking_skills",
        placeholder: "Enter details about thinking skills"
    },
    {
        label: "Orientation",
        field: "orientation",
        placeholder: "Enter details about orientation"
    },
    {
        label: "Behavior",
        field: "behavior",
        placeholder: "Enter details about behavior"
    }
];

const EMOTIONAL_FIELDS: Field[] = [
    {
        label: "Mood",
        field: "mood",
        placeholder: "Enter details about mood"
    },
    {
        label: "Social Interactions",
        field: "social_interactions",
        placeholder: "Enter details about social interactions"
    },
    {
        label: "Emotional Support Need",
        field: "emotional_support_need",
        placeholder: "Enter details about emotional support need"
    }
];

export const CognitiveFunctionSection = ({ 
    data = {}, 
    onChange = () => {} 
}: Props) => {
    const renderFields = (fields: Field[]) => (
        <YStack space="$4">
            {fields.map((field, index) => (
                <YStack key={index} space="$2">
                    <Text>{field.label}</Text>
                    <Input
                        value={data[field.field] as string}
                        onChangeText={(value) => onChange(field.field, value)}
                        placeholder={field.placeholder}
                        multiline
                        numberOfLines={3}
                        textAlignVertical="top"
                    />
                </YStack>
            ))}
        </YStack>
    );

    return (
        <YStack space="$4">
            <Card elevate>
                <Card.Header padded>
                    <H3>Mobility</H3>
                </Card.Header>
                <Card.Footer padded>
                    {renderFields(MOBILITY_FIELDS)}
                </Card.Footer>
            </Card>

            <Card elevate>
                <Card.Header padded>
                    <H3>Cognitive Function</H3>
                </Card.Header>
                <Card.Footer padded>
                    {renderFields(COGNITIVE_FIELDS)}
                </Card.Footer>
            </Card>

            <Card elevate>
                <Card.Header padded>
                    <H3>Emotional Well-being</H3>
                </Card.Header>
                <Card.Footer padded>
                    {renderFields(EMOTIONAL_FIELDS)}
                </Card.Footer>
            </Card>
        </YStack>
    );
};
