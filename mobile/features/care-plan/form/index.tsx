import TabScroll from "components/tabs/TabScroll";
import {
    ArrowLeft,
    ArrowRight,
    Check,
} from "lucide-react-native";
import { useState } from "react";
import { Button, XStack, YStack } from "tamagui";

import {
    Cognitive,
    CognitiveData,
} from "./components/Cognitive";
import {
    DiseaseTherapy,
    DiseaseTherapyData,
} from "./components/DiseaseTherapy";
import {
    Evaluation,
    EvaluationData,
} from "./components/Evaluation";
import { FormProgress } from "./components/FormProgress";
import {
    HouseholdKeeping,
    HouseholdKeepingData,
} from "./components/HouseholdKeeping";
import {
    Mobility,
    MobilityData,
} from "./components/Mobility";
import {
    OutdoorActivity,
    OutdoorActivityData,
} from "./components/OutdoorActivity";
import {
    PersonalDetails,
    PersonalDetailsData,
} from "./components/PersonalDetails";
import {
    SelfSustainability,
    SelfSustainabilityData,
} from "./components/SelfSustainability";
import {
    SocialContact,
    SocialContactData,
} from "./components/SocialContact";

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

interface FormData {
    personalDetails: PersonalDetailsData;
    mobility: MobilityData;
    cognitive: CognitiveData;
    selfSustainability: SelfSustainabilityData;
    diseaseTherapy: DiseaseTherapyData;
    socialContact: SocialContactData;
    outdoorActivity: OutdoorActivityData;
    householdKeeping: HouseholdKeepingData;
    evaluation: EvaluationData;
}

const INITIAL_FORM_DATA: FormData = {
    personalDetails: {
        beneficiaryId: "",
        assessment: "",
        bloodPressure: "",
        pulseRate: "",
        temperature: "",
        respiratoryRate: "",
    },
    mobility: {
        interventions: [],
    },
    cognitive: {
        interventions: [],
    },
    selfSustainability: {
        interventions: [],
    },
    diseaseTherapy: {
        interventions: [],
    },
    socialContact: {
        interventions: [],
    },
    outdoorActivity: {
        interventions: [],
    },
    householdKeeping: {
        interventions: [],
    },
    evaluation: {
        pictureUri: null,
        recommendations: "",
    },
};

const WCPForm = () => {
    const [currentStep, setCurrentStep] =
        useState(0);
    const [formData, setFormData] =
        useState<FormData>(INITIAL_FORM_DATA);

    const handlePrevious = () => {
        if (currentStep > 0) {
            setCurrentStep(currentStep - 1);
        }
    };

    const updateFormData = <
        K extends keyof FormData,
    >(
        step: K,
        data: Partial<FormData[K]>,
    ) => {
        setFormData((prev) => ({
            ...prev,
            [step]: { ...prev[step], ...data },
        }));
    };

    const renderStep = () => {
        switch (currentStep) {
            case 0:
                return (
                    <PersonalDetails
                        data={
                            formData.personalDetails
                        }
                        onChange={(data) =>
                            updateFormData(
                                "personalDetails",
                                data,
                            )
                        }
                    />
                );
            case 1:
                return (
                    <Mobility
                        data={formData.mobility}
                        onChange={(data) =>
                            updateFormData(
                                "mobility",
                                data,
                            )
                        }
                    />
                );
            case 2:
                return (
                    <Cognitive
                        data={formData.cognitive}
                        onChange={(data) =>
                            updateFormData(
                                "cognitive",
                                data,
                            )
                        }
                    />
                );
            case 3:
                return (
                    <SelfSustainability
                        data={
                            formData.selfSustainability
                        }
                        onChange={(data) =>
                            updateFormData(
                                "selfSustainability",
                                data,
                            )
                        }
                    />
                );
            case 4:
                return (
                    <DiseaseTherapy
                        data={
                            formData.diseaseTherapy
                        }
                        onChange={(data) =>
                            updateFormData(
                                "diseaseTherapy",
                                data,
                            )
                        }
                    />
                );
            case 5:
                return (
                    <SocialContact
                        data={
                            formData.socialContact
                        }
                        onChange={(data) =>
                            updateFormData(
                                "socialContact",
                                data,
                            )
                        }
                    />
                );
            case 6:
                return (
                    <OutdoorActivity
                        data={
                            formData.outdoorActivity
                        }
                        onChange={(data) =>
                            updateFormData(
                                "outdoorActivity",
                                data,
                            )
                        }
                    />
                );
            case 7:
                return (
                    <HouseholdKeeping
                        data={
                            formData.householdKeeping
                        }
                        onChange={(data) =>
                            updateFormData(
                                "householdKeeping",
                                data,
                            )
                        }
                    />
                );
            case 8:
                return (
                    <Evaluation
                        data={formData.evaluation}
                        onChange={(data) =>
                            updateFormData(
                                "evaluation",
                                data,
                            )
                        }
                    />
                );
            default:
                return null;
        }
    };

    const handleNext = () => {
        if (currentStep < FORM_STEPS.length - 1) {
            setCurrentStep(currentStep + 1);
        }
    };

    const handleSubmit = () => {
        // Handle form submission logic here
        console.log(
            "Form submitted with data:",
            formData,
        );
        // Reset form or navigate to another screen if needed
        setCurrentStep(0);
        setFormData(INITIAL_FORM_DATA);
    };

    return (
        <YStack style={{ flex: 1 }}>
            <FormProgress
                currentStep={currentStep}
                steps={FORM_STEPS}
                setStep={setCurrentStep}
            />

            <TabScroll style={{ flex: 1 }}>
                <YStack
                    gap="$4"
                    style={{ padding: 16 }}
                >
                    {renderStep()}
                </YStack>
            </TabScroll>

            <YStack
                style={{
                    borderTopWidth: 1,
                    padding: 16,
                    borderTopColor: "#e5e5e5",
                    backgroundColor: "#ffffff",
                }}
            >
                <XStack style={{ gap: 16 }}>
                    {currentStep > 0 && (
                        <Button
                            style={{ flex: 1 }}
                            onPress={
                                handlePrevious
                            }
                            icon={
                                <ArrowLeft
                                    size={16}
                                />
                            }
                            disabled={
                                currentStep === 0
                            }
                        >
                            Previous
                        </Button>
                    )}
                    {currentStep ===
                    FORM_STEPS.length - 1 ? (
                        <Button
                            style={{ flex: 1 }}
                            onPress={handleSubmit}
                            themeInverse
                        >
                            <Check size={16} />
                            Submit
                        </Button>
                    ) : (
                        <Button
                            style={{ flex: 1 }}
                            onPress={handleNext}
                        >
                            {currentStep === 0
                                ? "Start"
                                : "Next"}
                            <ArrowRight
                                size={16}
                            />
                        </Button>
                    )}
                </XStack>
            </YStack>
        </YStack>
    );
};

export default WCPForm;
