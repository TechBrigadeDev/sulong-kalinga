import { useServiceRequestForm } from "features/portal/emergency-service/service/form/form";
import { IServiceRequestForm } from "features/portal/emergency-service/service/form/schema";
import { useServiceRequest } from "features/portal/emergency-service/service/hook";
import { SubmitErrorHandler } from "react-hook-form";
import { showToastable } from "react-native-toastable";
import { Button, Spinner } from "tamagui";

const SubmitServiceRequest = () => {
    const form = useServiceRequestForm();
    const {
        mutate: submitServiceRequest,
        isPending: isSubmitting,
    } = useServiceRequest();

    const onSuccess = async (
        data: IServiceRequestForm,
    ) => {
        try {
            console.log(
                "Submitting service request data:",
                data,
            );
            submitServiceRequest(data);
            // Here you can handle the successful submission, e.g., send data to an API
            // For now, we'll just log the data
            showToastable({
                message:
                    "Service request submitted successfully!",
                status: "success",
                duration: 3000,
            });

            form.reset({
                service_type_id: "",
                service_date: "",
                service_time: "",
                message: "",
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

    const disabled =
        form.formState.isSubmitting ||
        isSubmitting;

    return (
        <Button
            onPress={handleSubmit}
            disabled={disabled}
            size="$5"
            mt="$4"
            theme="blue"
            fontSize="$5"
            fontWeight="600"
        >
            {form.formState.isSubmitting ||
                (isSubmitting && (
                    <Spinner
                        size="small"
                        color="$color"
                    />
                )) ||
                "Submit Service Request"}
        </Button>
    );
};

export default SubmitServiceRequest;
