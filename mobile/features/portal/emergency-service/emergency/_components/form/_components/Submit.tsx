import { useEmergencyForm } from "features/portal/emergency-service/emergency/_components/form/form";
import { IEmergencyForm } from "features/portal/emergency-service/emergency/_components/form/interface";
import { SubmitErrorHandler } from "react-hook-form";
import { Button } from "tamagui";

const SubmitEmergency = () => {
    const form = useEmergencyForm();

    const onSuccess = async (
        data: IEmergencyForm,
    ) => {
        try {
            console.log(
                "Submitting emergency data:",
                data,
            );
            // Here you can handle the successful submission, e.g., send data to an API
        } catch (error) {
            console.error(
                "Error submitting form:",
                error,
            );
        }
    };

    const onError: SubmitErrorHandler<
        IEmergencyForm
    > = (errors) => {
        // Handle form validation errors
        console.error(
            "Form submission errors:",
            errors,
        );
        // You can show a toast or alert with the error messages
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
            mt="$8"
            theme="blue"
            fontSize="$5"
            fontWeight="600"
        >
            {/* <Spinner /> */}
            Submit
        </Button>
    );
};

export default SubmitEmergency;
