import TabScroll from "components/tabs/TabScroll";
import {
    Stack,
    useFocusEffect,
} from "expo-router";
import { IRecordDetail } from "features/records/interface";
import {
    ArrowLeft,
    ArrowRight,
} from "lucide-react-native";
import { useCallback, useEffect } from "react";
import { useSafeAreaInsets } from "react-native-safe-area-context";
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
import {
    CarePlanForm,
    useCarePlanForm,
} from "./form";
import { useCarePlanFormStore } from "./store";

const FORM_STEPS = [
    { label: "Personal Details" },
    { label: "Mobility" },
    { label: "Cognitive/Communication" },
    { label: "Self-sustainability" },
    { label: "Disease/Therapy Handling" },
    { label: "Daily life/Social contact" },
    { label: "Outdoor Activities" },
    { label: "Household Keeping" },
    { label: "Evaluation" },
];

interface Props {
    record?: IRecordDetail;
}

const Form = ({ record }: Props) => {
    const { bottom } = useSafeAreaInsets();
    const {
        setRecord,
        currentStep,
        setCurrentStep,
        resetStep,
    } = useCarePlanFormStore();

    const { reset: formReset, getValues } =
        useCarePlanForm();

    const formHasValues =
        Object.keys(getValues()).length > 0;

    // Helper function to map interventions for each category
    const mapInterventions = (
        record: IRecordDetail,
        categoryId: number,
    ) => {
        return record.interventions
            .filter(
                (intervention) =>
                    intervention.care_category_id ===
                    categoryId,
            )
            .map((intervention) => ({
                id: intervention.wcp_intervention_id.toString(),
                name:
                    intervention.description ||
                    "",
                minutes:
                    parseFloat(
                        intervention.duration_minutes,
                    ) || 0,
                isCustom:
                    intervention.intervention_id ===
                    null,
                interventionId:
                    intervention.intervention_id ||
                    undefined,
                categoryId:
                    intervention.care_category_id ||
                    undefined,
                description:
                    intervention.intervention_id ===
                    null
                        ? intervention.description ||
                          undefined
                        : undefined,
            }));
    };

    useEffect(() => {
        if (record) {
            setRecord(record);
            formReset({
                personalDetails: {
                    beneficiaryId:
                        record.beneficiary.beneficiary_id.toString(),
                    illness:
                        record.illnesses?.join(
                            ", ",
                        ) || "",
                    assessment:
                        record.assessment || "",
                    bloodPressure:
                        record.vital_signs
                            .blood_pressure,
                    pulseRate:
                        record.vital_signs
                            .pulse_rate,
                    temperature:
                        parseFloat(
                            record.vital_signs
                                .body_temperature,
                        ) || 36.5,
                    respiratoryRate:
                        record.vital_signs
                            .respiratory_rate,
                },
                mobility: mapInterventions(
                    record,
                    1, // Mobility
                ),
                cognitive: mapInterventions(
                    record,
                    2, // Cognitive/Communication (corrected from 7 to 2)
                ),
                selfSustainability:
                    mapInterventions(record, 3), // Self-sustainability (corrected from 8 to 3)
                diseaseTherapy: mapInterventions(
                    record,
                    4, // Disease/Therapy Handling (corrected from 9 to 4)
                ),
                socialContact: mapInterventions(
                    record,
                    5, // Daily life/Social contact (corrected from 10 to 5)
                ),
                outdoorActivity: mapInterventions(
                    record,
                    6, // Outdoor Activities (corrected from 11 to 6)
                ),
                householdKeeping:
                    mapInterventions(record, 7), // Household Keeping (corrected from 12 to 7)
                evaluation: {
                    pictureUri:
                        record.photo_url || "",
                    recommendations:
                        record.evaluation_recommendations ||
                        "",
                },
            });
        }

        return () => {
            console.log(
                "Form reset on record change or unmount",
            );
            formReset({});
        };
    }, [
        record,
        setRecord,
        formReset,
        formHasValues,
    ]);

    useFocusEffect(
        useCallback(() => {
            return () => {
                setRecord(null);
                resetStep();
                formReset();
            };
        }, [resetStep, formReset, setRecord]),
    );

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
        <>
            <YStack flex={1}>
                <FormProgress
                    currentStep={currentStep}
                    steps={FORM_STEPS}
                    setStep={setCurrentStep}
                />

                <TabScroll
                    flex={1}
                    showScrollUp={false}
                >
                    <YStack gap="$4" flex={1}>
                        {renderStep()}
                    </YStack>
                </TabScroll>

                <YStack
                    borderTopWidth={1}
                    p="$4"
                    bg="$background"
                    marginBlockEnd={bottom}
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
        </>
    );
};

const WCPForm = (props: Props) => {
    return (
        <CarePlanForm>
            <Form {...props} />
        </CarePlanForm>
    );
};

export default WCPForm;
