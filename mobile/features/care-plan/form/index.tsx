import TabScroll from "components/tabs/TabScroll";
import { Stack } from "expo-router";
import {
    ArrowLeft,
    ArrowRight,
} from "lucide-react-native";
import { useState } from "react";
import {
    Button,
    View,
    XStack,
    YStack,
} from "tamagui";

import { Cognitive } from "./components/Cognitive";
import { DiseaseTherapy } from "./components/DiseaseTherapy";
import { Evaluation } from "./components/Evaluation";
import { FormProgress } from "./components/FormProgress";
import { HouseholdKeeping } from "./components/HouseholdKeeping";
import { Mobility } from "./components/Mobility";
import { OutdoorActivity } from "./components/OutdoorActivity";
import { PersonalDetails } from "./components/PersonalDetails";
import { SelfSustainability } from "./components/SelfSustainability";
import { SocialContact } from "./components/SocialContact";
import Submit from "./components/Submit";
import { CarePlanForm } from "./form";

const FORM_STEPS = [
    { label: "Personal Details" },
    { label: "Mobility" },
    { label: "Cognitive/Communication" },
    { label: "Self-Sustainability" },
    { label: "Disease/Therapy" },
    { label: "Social Contact" },
    { label: "Outdoor Activities" },
    { label: "Household Keeping" },
    { label: "Evaluation" },
];

const WCPForm = () => {
    const [currentStep, setCurrentStep] =
        useState(0);

    const handleNext = () => {
        if (currentStep < FORM_STEPS.length - 1) {
            setCurrentStep(currentStep + 1);
        }
    };

    const handlePrevious = () => {
        if (currentStep > 0) {
            setCurrentStep(currentStep - 1);
        }
    };

    const renderStep = () => {
        switch (currentStep) {
            case 0:
                return <PersonalDetails />;
            case 1:
                return <Mobility />;
            case 2:
                return <Cognitive />;
            case 3:
                return <SelfSustainability />;
            case 4:
                return <DiseaseTherapy />;
            case 5:
                return <SocialContact />;
            case 6:
                return <OutdoorActivity />;
            case 7:
                return <HouseholdKeeping />;
            case 8:
                return <Evaluation />;
            default:
                return <PersonalDetails />;
        }
    };

    const isLastStep =
        currentStep === FORM_STEPS.length - 1;

    return (
        <CarePlanForm>
            <YStack flex={1}>
                <FormProgress
                    currentStep={currentStep}
                    steps={FORM_STEPS}
                    setStep={setCurrentStep}
                />

                <TabScroll flex={1}>
                    <YStack gap="$4" p="$4">
                        {renderStep()}
                    </YStack>
                </TabScroll>

                <YStack
                    borderTopWidth={1}
                    p="$4"
                    bg="$background"
                >
                    <XStack
                        gap="$4"
                        display="flex"
                    >
                        {currentStep > 0 && (
                            <Button
                                flex={1}
                                onPress={
                                    handlePrevious
                                }
                                icon={
                                    <ArrowLeft
                                        size={16}
                                    />
                                }
                                variant="outlined"
                            >
                                Previous
                            </Button>
                        )}

                        {isLastStep ? (
                            <View>
                                <Submit />
                            </View>
                        ) : (
                            <Button
                                flex={1}
                                onPress={
                                    handleNext
                                }
                                theme="blue"
                                icon={
                                    <ArrowRight
                                        size={16}
                                    />
                                }
                            >
                                {currentStep === 0
                                    ? "Start"
                                    : "Next"}
                            </Button>
                        )}
                    </XStack>
                </YStack>
            </YStack>
            <Stack.Screen
                options={{
                    headerTitle:
                        FORM_STEPS[currentStep]
                            .label,
                }}
            />
        </CarePlanForm>
    );
};

export default WCPForm;
