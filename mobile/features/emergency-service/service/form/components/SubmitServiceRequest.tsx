import { useServiceRequestForm } from "features/emergency-service/service/form-hook";
import { IServiceRequestForm } from "features/emergency-service/service/schema";
import { SubmitErrorHandler } from "react-hook-form";
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
            alert(
                "Service request submitted successfully!",
            );
        } catch (error) {
            console.error(
                "Error submitting service request:",
                error,
            );
            alert(
                "Failed to submit service request. Please try again.",
            );
        }
    };

    const onError: SubmitErrorHandler<
        IServiceRequestForm
    > = (errors) => {
        console.error(
            "Form submission errors:",
            errors,
        );
        // Find the first error message to show
        const firstError = Object.values(errors)[0];
        if (firstError?.message) {
            alert(firstError.message);
        }
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
