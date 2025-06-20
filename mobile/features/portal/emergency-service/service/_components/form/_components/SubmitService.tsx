import { useServiceFormContext } from "features/portal/emergency-service/service/_components/form/form";
import { IServiceForm } from "features/portal/emergency-service/service/_components/form/interface";
import { useEmergencyServiceStore } from "features/portal/emergency-service/store";
import { IEmergencyServiceRequest } from "features/portal/emergency-service/type";
import { useEffect, useState } from "react";
import { Button, Spinner } from "tamagui";

const SubmitService = () => {
    const form = useServiceFormContext();

    const [currentRequest, setCurrentRequest] =
        useState<IEmergencyServiceRequest | null>(
            null,
        );
    const store = useEmergencyServiceStore();
    const request = store.getState().request;

    useEffect(() => {
        console.log(
            "Current request in SubmitService:",
            request,
        );
        if (request?.type === "emergency") {
            return;
        } else if (request) {
            setCurrentRequest(request);
            form.reset({
                service_type_id:
                    request.service_type_id?.toString(),
                service_date:
                    request.service_date,
                service_time:
                    request.service_time,
                message: request.description,
            } as IServiceForm);
        } else {
            setCurrentRequest(null);
        }

        store.subscribe(() => {
            const updatedRequest =
                store.getState().request;
            if (updatedRequest) {
                setCurrentRequest(updatedRequest);
                form.reset({
                    service_type_id:
                        updatedRequest.service_type_id?.toString(),
                    service_date:
                        updatedRequest.service_date,
                    service_time:
                        updatedRequest.service_time,
                    message: updatedRequest.description,
                } as IServiceForm);
            } else {
                setCurrentRequest(null);
                form.reset({
                    service_type_id: "",
                    service_date: "",
                    service_time: "",
                    message: "",
                } as IServiceForm);
            }
        });
    }, [store, request, form]);

    const isEditing =
        currentRequest?.type === "service";

    const resetForm = () => {
        form.reset({
            service_type_id: "",
            service_date: "",
            service_time: "",
            message: "",
        });
        store.setState({
            request: null,
        });
    };

    const Edit = () =>
        isEditing && (
            <Button
                onPress={resetForm}
                disabled={form.state.isSubmitting}
                size="$5"
                theme="red"
                fontSize="$5"
                fontWeight="600"
            >
                Reset
            </Button>
        );

    return (
        <form.Subscribe
            selector={(state) => state}
        >
            {(state) => (
                <>
                    <Button
                        onPress={
                            form.handleSubmit
                        }
                        disabled={
                            state.isSubmitting
                        }
                        size="$5"
                        mt="$1"
                        theme="blue"
                        fontSize="$5"
                        fontWeight="600"
                    >
                        {form.state
                            .isSubmitting && (
                            <Spinner
                                size="small"
                                mr="$2"
                                color="$white1"
                            />
                        )}
                        {isEditing
                            ? "Update"
                            : "Submit"}
                    </Button>
                    <Edit />
                </>
            )}
        </form.Subscribe>
    );
};

export default SubmitService;
