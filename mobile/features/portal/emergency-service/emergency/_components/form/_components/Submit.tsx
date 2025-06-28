import { useEmergencyForm } from "features/portal/emergency-service/emergency/_components/form/form";
import { IEmergencyForm } from "features/portal/emergency-service/emergency/_components/form/interface";
import { useEmergencyRequest } from "features/portal/emergency-service/emergency/hook";
import { SubmitErrorHandler } from "react-hook-form";
import { showToastable } from "react-native-toastable";
import { Button, Spinner } from "tamagui";

const SubmitEmergency = () => {
    const form = useEmergencyForm();
    const {
        mutate: submitEmergencyRequest,
        isPending: isSubmitting,
    } = useEmergencyRequest();

    const onSuccess = async (
        data: IEmergencyForm,
    ) => {
        try {
            submitEmergencyRequest(data);

            showToastable({
                title: "Request Submitted",
                message:
                    "Your emergency request has been submitted successfully.",
                status: "success",
            });
            form.reset({
                message: "",
                emergency_type_id: "",
            });
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

    const disabled =
        form.formState.isSubmitting ||
        isSubmitting;

    return (
        <Button
            onPress={handleSubmit}
            disabled={disabled}
            size="$5"
            mt="$8"
            theme="blue"
            fontSize="$5"
            fontWeight="600"
        >
            {form.formState.isSubmitting ||
                (isSubmitting && (
                    <Spinner
                        size="small"
                        mr="$2"
                        color="$white1"
                    />
                ))}
            Submit
        </Button>
    );
};

export default SubmitEmergency;
