import { useCarePlanForm } from "features/care-plan/form/form";
import { CarePlanFormData } from "features/care-plan/form/type";
import { useSubmitCarePlanForm } from "features/care-plan/hook";
import {
    SubmitErrorHandler,
    SubmitHandler,
} from "react-hook-form";
import { showToastable } from "react-native-toastable";
import {
    Button,
    Spinner,
    Text,
    View,
} from "tamagui";

const SubmitCarePlanForm = () => {
    const { mutateAsync, isPending } =
        useSubmitCarePlanForm({
            onError: async (error) => {
                console.error(
                    "Error submitting care plan form:",
                    error,
                );
                showToastable({
                    message:
                        "An error occurred while submitting the care plan. Please try again.",
                    status: "danger",
                    duration: 4000,
                });
            },
        });
    const { handleSubmit, formState } =
        useCarePlanForm();

    const onSubmit: SubmitHandler<
        CarePlanFormData
    > = async (data) => {
        console.log(
            "Form submitted with data:",
            data,
        );

        try {
            await mutateAsync(data);
        } catch (error) {
            console.error(
                "Error submitting form:",
                error,
            );
        }
    };

    const onError: SubmitErrorHandler<
        CarePlanFormData
    > = (errors) => {
        /**
         * {"personalDetails": {"pulseRate": {"message": "Pulse rate must be between 40 and 200", "ref": [Object], "type": "too_small"}}}
         */

        // Helper function to convert technical field paths to user-friendly names
        const getFieldDisplayName = (
            path: string,
        ): string => {
            const fieldMappings: Record<
                string,
                string
            > = {
                "personalDetails.beneficiaryId":
                    "Beneficiary Selection",
                "personalDetails.illness":
                    "Illness",
                "personalDetails.assessment":
                    "Assessment",
                "personalDetails.bloodPressure":
                    "Blood Pressure",
                "personalDetails.pulseRate":
                    "Pulse Rate",
                "personalDetails.temperature":
                    "Temperature",
                "personalDetails.respiratoryRate":
                    "Respiratory Rate",
                mobility:
                    "Mobility Interventions",
                cognitive:
                    "Cognitive/Communication Interventions",
                selfSustainability:
                    "Self-Sustainability Interventions",
                diseaseTherapy:
                    "Disease/Therapy Interventions",
                socialContact:
                    "Social Contact Interventions",
                outdoorActivity:
                    "Outdoor Activity Interventions",
                householdKeeping:
                    "Household Keeping Interventions",
                "evaluation.pictureUri":
                    "Picture Upload",
                "evaluation.recommendations":
                    "Recommendations",
            };

            return (
                fieldMappings[path] ||
                path
                    .split(".")
                    .map(
                        (part) =>
                            part
                                .charAt(0)
                                .toUpperCase() +
                            part.slice(1),
                    )
                    .join(" - ")
            );
        };

        // Helper function to recursively extract error messages from nested error object
        const extractErrorMessages = (
            errorObj: any,
            path: string = "",
        ): {
            path: string;
            message: string;
        }[] => {
            const errorList: {
                path: string;
                message: string;
            }[] = [];

            for (const [
                key,
                value,
            ] of Object.entries(errorObj)) {
                const currentPath = path
                    ? `${path}.${key}`
                    : key;

                if (
                    value &&
                    typeof value === "object"
                ) {
                    // Check if this is a field error (has message property)
                    if (
                        "message" in value &&
                        typeof value.message ===
                            "string"
                    ) {
                        errorList.push({
                            path: currentPath,
                            message:
                                value.message,
                        });
                    } else {
                        // Recursively check nested objects
                        errorList.push(
                            ...extractErrorMessages(
                                value,
                                currentPath,
                            ),
                        );
                    }
                }
            }

            return errorList;
        };

        // Extract all error messages
        const errorMessages =
            extractErrorMessages(errors);

        // Combine all errors into a single toast
        if (errorMessages.length > 0) {
            const errorText = errorMessages
                .map(
                    ({ path, message }) =>
                        `${getFieldDisplayName(path)}: ${message}\n`,
                )
                .join("\n");

            showToastable({
                message: `${errorText}`,
                status: "danger",
                duration: 6000,
            });
        }
    };

    const onPress = async () => {
        if (formState.isSubmitting) {
            return;
        }

        try {
            await handleSubmit(
                onSubmit,
                onError,
            )();
        } catch (error) {
            console.error(
                "Error handling form submission:",
                error,
            );
        }
    };

    return (
        <Button
            style={{
                flex: 1,
            }}
            onPress={onPress}
            themeInverse
            disabled={
                formState.isSubmitting ||
                isPending
            }
        >
            {formState.isSubmitting && (
                <View>
                    <Spinner size="small" />
                </View>
            )}
            <Text>Save Weekly Care Plan</Text>
        </Button>
    );
};

export default SubmitCarePlanForm;
