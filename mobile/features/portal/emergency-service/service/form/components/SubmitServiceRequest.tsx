import { useServiceRequestForm } from "features/portal/emergency-service/service/form/form";
import { IServiceRequestForm } from "features/portal/emergency-service/service/form/schema";
import { SubmitErrorHandler } from "react-hook-form";
import { showToastable } from "react-native-toastable";
import { Button } from "tamagui";

const SubmitServiceRequest = () => {
    const form = useServiceRequestForm();

    const onSuccess = async (
        data: IServiceRequestForm,
    ) => {
        try {
            console.log(
                "Submitting service request data:",
                data,
            );
            // Here you can handle the successful submission, e.g., send data to an API
            // For now, we'll just log the data
            showToastable({
                message:
                    "Service request submitted successfully!",
                status: "success",
                duration: 3000,
            });
        } catch (error) {
            console.error(
                "Error submitting service request:",
                error,
            );
            showToastable({
                message:
                    "Failed to submit service request. Please try again later.",
                status: "danger",
                duration: 3000,
            });
        }
    };

    const onError: SubmitErrorHandler<
        IServiceRequestForm
    > = (errors) => {
        console.error(
            "Form submission errors:",
            errors,
        );

        showToastable({
            message: Object.values(errors)
                .map((error) => error.message)
                .join("\n"),
            status: "danger",
            duration: 3000,
        });
    };

    const handleSubmit = async () => {
        try {
            await form.handleSubmit(
                onSuccess,
                onError,
            )();
        } catch (error) {
            console.error(
                "Error submitting form:",
                error,
            );
        }
    };

    return (
        <Button
            onPress={handleSubmit}
            disabled={form.formState.isSubmitting}
            size="$5"
            mt="$4"
            theme="blue"
            fontSize="$5"
            fontWeight="600"
        >
            {form.formState.isSubmitting
                ? "Submitting..."
                : "Submit Request"}
        </Button>
    );
};

export default SubmitServiceRequest;
