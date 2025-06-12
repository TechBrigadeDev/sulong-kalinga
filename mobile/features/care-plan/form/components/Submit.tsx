import { useCarePlanForm } from "features/care-plan/form/form";
import { CarePlanFormData } from "features/care-plan/form/type";
import { useSubmitCarePlanForm } from "features/care-plan/hook";
import { SubmitHandler } from "react-hook-form";
import { Button } from "tamagui";

const SubmitCarePlanForm = () => {
    const { mutateAsync, isPending } =
        useSubmitCarePlanForm();
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

    const onError = (errors: any) => {
        console.log(
            "Form validation errors:",
            errors,
        );
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
    }

    return (
        <Button
            style={{
                flex: 1,
            }}
            onPress={onPress}
            themeInverse
            disabled={formState.isSubmitting}
        >
            {formState.isSubmitting
                ? "Submitting..."
                : "Submit Personal Details"}
        </Button>
    );
};

export default SubmitCarePlanForm;
