import TabScroll from "components/tabs/TabScroll";
import { useLocalSearchParams } from "expo-router";
import { useCarePlanById } from "features/portal/care-plan/hook";
import React from "react";
import { Spinner, Text, YStack } from "tamagui";

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

    return (
        <TabScroll
            flex={1}
            style={{ backgroundColor: "#fff" }}
            showsVerticalScrollIndicator={false}
            contentContainerStyle={{
                paddingBlockEnd: 150,
            }}
            keyboardShouldPersistTaps="handled"
            bounces={false}
            tabbed
        >
            <YStack
                gap="$3"
                style={{ padding: 16 }}
            >
                <PersonalDetails
                    data={{
                        beneficiary:
                            data.beneficiary,
                        care_worker:
                            data.care_worker,
                        plan_date:
                            data.created_at,
                        status: data.acknowledge_status,
                        acknowledged_by:
                            data.who_acknowledged,
                    }}
                />

                <Assessment
                    assessment={data.assessment}
                    illnesses={data.illnesses}
                />

                <VitalSigns
                    vitalSigns={data?.vital_signs}
                />

                <EvaluationRecommendations
                    evaluation={
                        data.evaluation_recommendations
                    }
                />

                <CareInterventions
                    interventions={
                        data?.interventions || []
                    }
                />
            </YStack>
        </TabScroll>
    );
};

export default CarePlanDetail;
