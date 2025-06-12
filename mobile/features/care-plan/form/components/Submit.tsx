import { useCarePlanForm } from "features/care-plan/form/form";
import { CarePlanFormData } from "features/care-plan/form/type";
import { SubmitHandler } from "react-hook-form";
import { Button } from "tamagui";

const SubmitCarePlanForm = () => {
    const { handleSubmit, formState } =
        useCarePlanForm();

    const onSubmit: SubmitHandler<
        CarePlanFormData
    > = (data) => {
        console.log(
            "Form submitted with data:",
            data,
        );
        // TODO: handle API call
    };

    const onError = (errors: any) => {
        console.log(
            "Form validation errors:",
            errors,
        );
    };

    return (
        <Button
            style={{
                flex: 1,
            }}
            onPress={handleSubmit(
                onSubmit,
                onError,
            )}
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
