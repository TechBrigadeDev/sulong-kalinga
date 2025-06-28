import { useLocalSearchParams } from "expo-router";
import { useCarePlanById } from "features/portal/care-plan/hook";
import React from "react";
import {
    ScrollView,
    Spinner,
    Text,
    YStack,
} from "tamagui";

import {
    Assessment,
    CareInterventions,
    EvaluationRecommendations,
    PersonalDetails,
    VitalSigns,
} from "./components";

const CarePlanDetail = () => {
    const { id } = useLocalSearchParams<{
        id: string;
    }>();
    const {
        data: response,
        isLoading,
        error,
    } = useCarePlanById(id!);

    if (isLoading) {
        return (
            <YStack
                flex={1}
                items="center"
                justify="center"
                gap="$3"
            >
                <Spinner
                    size="large"
                    color="blue"
                />
                <Text
                    fontSize="$4"
                    color="$color"
                >
                    Loading care plan details...
                </Text>
            </YStack>
        );
    }

    if (error) {
        return (
            <YStack
                flex={1}
                items="center"
                justify="center"
                gap="$3"
                p="$4"
            >
                <Text
                    fontSize="$5"
                    fontWeight="600"
                    color="$red10"
                >
                    Error Loading Care Plan
                </Text>
                <Text
                    fontSize="$4"
                    color="$color"
                    text="center"
                    lineHeight="$1"
                >
                    {error.message ||
                        "Failed to load care plan details. Please try again."}
                </Text>
            </YStack>
        );
    }

    if (!response?.data) {
        return (
            <YStack
                flex={1}
                items="center"
                justify="center"
                gap="$3"
            >
                <Text
                    fontSize="$4"
                    color="$color"
                >
                    No care plan data available
                </Text>
            </YStack>
        );
    }

    const data = response.data;
    const illnesses = JSON.parse(
        data.illnesses || "[]",
    );

    return (
        <ScrollView
            flex={1}
            style={{ backgroundColor: "#fff" }}
            showsVerticalScrollIndicator={false}
            contentContainerStyle={{
                paddingBlockEnd: 110,
            }}
            keyboardShouldPersistTaps="handled"
            bounces={false}
        >
            <YStack
                gap="$3"
                style={{ padding: 16 }}
            >
                <PersonalDetails
                    data={{
                        beneficiary:
                            data.beneficiary,
                        author: data.author,
                        care_worker:
                            data.care_worker,
                        plan_date:
                            data.created_at,
                        status: data.acknowledged_by_beneficiary
                            ? "acknowledged"
                            : "pending",
                    }}
                />

                <Assessment
                    assessment={data.assessment}
                    illnesses={illnesses}
                />

                <VitalSigns
                    vitalSigns={
                        data?.vital_signs
                            ? [data?.vital_signs]
                            : []
                    }
                />

                <EvaluationRecommendations
                    evaluation={
                        data.evaluation_recommendations
                    }
                />

                <CareInterventions
                    interventions={
                        data?.interventions
                    }
                />
            </YStack>
        </ScrollView>
    );
};

export default CarePlanDetail;
