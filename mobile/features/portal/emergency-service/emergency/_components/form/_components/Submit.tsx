import { useEmergencyFormContext } from "features/portal/emergency-service/emergency/_components/form/form";
import { useEmergencyServiceStore } from "features/portal/emergency-service/store";
import { IEmergencyServiceRequest } from "features/portal/emergency-service/type";
import { useEffect, useState } from "react";
import { Button, Spinner } from "tamagui";

const SubmitEmergency = () => {
    const store = useEmergencyServiceStore();
    const form = useEmergencyFormContext();

    const [currentRequest, setCurrentRequest] =
        useState<IEmergencyServiceRequest | null>(
            null,
        );
    const request = store.getState().request;

    useEffect(() => {
        if (
            request &&
            request.type === "emergency"
        ) {
            setCurrentRequest(request);
            // form.reset({
            //     message: request.description,
            //     emergency_type_id:
            //         request.emergency_type_id.toString(),
            // });
        } else {
            setCurrentRequest(null);
            form.reset({
                message: "",
                emergency_type_id: "",
            });
        }

        store.subscribe(() => {
            const updatedRequest =
                store.getState().request;
            if (updatedRequest) {
                setCurrentRequest(updatedRequest);
                form.reset({
                    message:
                        updatedRequest?.description,
                    emergency_type_id:
                        updatedRequest.emergency_type_id?.toString(),
                });
            } else {
                setCurrentRequest(null);
                form.reset({
                    message: "",
                    emergency_type_id: "",
                });
            }
        });
    }, [store, request, form]);

    const isEditing =
        currentRequest?.type === "emergency";

    const resetForm = () => {
        form.reset({
            message: "",
            emergency_type_id: "",
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
                        mt="$8"
                        theme="light_blue"
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
                        {currentRequest?.id
                            ? "Update"
                            : "Submit"}
                        {form.state.isSubmitting}
                    </Button>
                    <Edit />
                </>
            )}
        </form.Subscribe>
    );
};

export default SubmitEmergency;
