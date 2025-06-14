import { BeneficiaryFormValues } from "features/user-management/components/beneficiaries/BeneficiaryForm/schema";
import {
    Controller,
    useFormContext,
} from "react-hook-form";
import { Card, H3, Text, YStack } from "tamagui";

import { EnhancedInput } from "./EnhancedInput";

interface CareNeed {
    label: string;
    frequencyField:
        | "mobility_frequency"
        | "cognitive_frequency"
        | "self_sustainability_frequency"
        | "disease_therapy_frequency"
        | "daily_life_frequency"
        | "outdoor_frequency"
        | "household_frequency";
    assistanceField:
        | "mobility_assistance"
        | "cognitive_assistance"
        | "self_sustainability_assistance"
        | "disease_therapy_assistance"
        | "daily_life_assistance"
        | "outdoor_assistance"
        | "household_assistance";
}

const CARE_NEEDS: CareNeed[] = [
    {
        label: "Mobility",
        frequencyField: "mobility_frequency",
        assistanceField: "mobility_assistance",
    },
    {
        label: "Cognitive / Communication",
        frequencyField: "cognitive_frequency",
        assistanceField: "cognitive_assistance",
    },
    {
        label: "Self-sustainability",
        frequencyField:
            "self_sustainability_frequency",
        assistanceField:
            "self_sustainability_assistance",
    },
    {
        label: "Disease / Therapy Handling",
        frequencyField:
            "disease_therapy_frequency",
        assistanceField:
            "disease_therapy_assistance",
    },
    {
        label: "Daily Life / Social Contact",
        frequencyField: "daily_life_frequency",
        assistanceField: "daily_life_assistance",
    },
    {
        label: "Outdoor Activities",
        frequencyField: "outdoor_frequency",
        assistanceField: "outdoor_assistance",
    },
    {
        label: "Household Keeping",
        frequencyField: "household_frequency",
        assistanceField: "household_assistance",
    },
];

export const CareNeedsSection = () => {
    return (
        <Card elevate>
            <Card.Header padded>
                <H3>Care Needs</H3>
            </Card.Header>
            <YStack p="$4" gap="$3">
                <MobilityCare />
                <CognitiveCare />
                <SelfSustainabilityCare />
                <DiseaseTherapyCare />
                <DailyLifeCare />
                <OutdoorCare />
                <HouseholdCare />
            </YStack>
        </Card>
    );
};

const CareNeedComponent = ({
    label,
    frequencyField,
    assistanceField,
}: CareNeed) => {
    const { control } =
        useFormContext<BeneficiaryFormValues>();

    return (
        <YStack gap="$3" p="$4" mb="$4">
            <Text fontSize="$5" fontWeight="600">
                {label}
            </Text>
            <YStack gap="$3">
                <Controller
                    control={control}
                    name={frequencyField}
                    render={({
                        field,
                        fieldState,
                    }) => (
                        <EnhancedInput
                            label="Frequency"
                            value={
                                field.value || ""
                            }
                            onChangeText={
                                field.onChange
                            }
                            onBlur={field.onBlur}
                            placeholder="How often is this needed? (e.g., daily, weekly)"
                            error={
                                fieldState.error
                                    ?.message
                            }
                            multiline
                            numberOfLines={2}
                            textAlignVertical="top"
                        />
                    )}
                />
                <Controller
                    control={control}
                    name={assistanceField}
                    render={({
                        field,
                        fieldState,
                    }) => (
                        <EnhancedInput
                            label="Assistance Required"
                            value={
                                field.value || ""
                            }
                            onChangeText={
                                field.onChange
                            }
                            onBlur={field.onBlur}
                            placeholder="What type of help is needed?"
                            error={
                                fieldState.error
                                    ?.message
                            }
                            multiline
                            numberOfLines={2}
                            textAlignVertical="top"
                        />
                    )}
                />
            </YStack>
        </YStack>
    );
};

const MobilityCare = () => (
    <CareNeedComponent {...CARE_NEEDS[0]} />
);

const CognitiveCare = () => (
    <CareNeedComponent {...CARE_NEEDS[1]} />
);

const SelfSustainabilityCare = () => (
    <CareNeedComponent {...CARE_NEEDS[2]} />
);

const DiseaseTherapyCare = () => (
    <CareNeedComponent {...CARE_NEEDS[3]} />
);

const DailyLifeCare = () => (
    <CareNeedComponent {...CARE_NEEDS[4]} />
);

const OutdoorCare = () => (
    <CareNeedComponent {...CARE_NEEDS[5]} />
);

const HouseholdCare = () => (
    <CareNeedComponent {...CARE_NEEDS[6]} />
);
