import { EmergencyServiceFormProp } from "features/portal/emergency-service/emergency/interface";
import {
    useEditnServiceRequest,
    useServiceRequest,
} from "features/portal/emergency-service/service/hook";
import { useEmergencyServiceStore } from "features/portal/emergency-service/store";
import { ClipboardList } from "lucide-react-native";
import { useEffect } from "react";
import { KeyboardAvoidingView } from "react-native";
import {
    Card,
    H5,
    Text,
    XStack,
    YStack,
} from "tamagui";
import { useStore } from "zustand";

import {
    serviceFormOpts,
    useServiceForm,
} from "./form";

const ServiceAssistanceForm = ({
    onSubmitSuccess,
}: EmergencyServiceFormProp) => {
    const store = useEmergencyServiceStore();
    const { mutateAsync: submitServiceRequest } =
        useServiceRequest();

    const { mutateAsync: editServiceRequest } =
        useEditnServiceRequest();

    const form = useServiceForm({
        ...serviceFormOpts,
        onSubmit: async ({ value, formApi }) => {
            const request =
                store.getState().request;

            try {
                if (request && request.id) {
                    await editServiceRequest({
                        id: request.id.toString(),
                        data: value,
                    });
                } else {
                    // If no request exists, create a new one
                    await submitServiceRequest(
                        value,
                    );
                }

                onSubmitSuccess?.();

                formApi.reset({
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
            }
        },
    });

    // Use reactive subscription with useStore for context-based stores
    const request = useStore(
        store,
        (state) => state.request,
    );
    
    useEffect(() => {
        if (request?.type === "emergency") {
            return;
        }

        if (request) {
            console.log(
                "Service form: Populating form with request data:",
                request,
            );
            form.reset({
                service_type_id:
                    request.service_type_id.toString(),
                service_date:
                    request.service_date,
                service_time:
                    request.service_time,
                message: request.description,
            });
        }
    }, [request, form]);
    return (
        <KeyboardAvoidingView style={{ flex: 1 }}>
            <Card
                marginBottom="$2"
                borderRadius={8}
                borderWidth={1}
                elevate
                gap="$4"
            >
                <YStack p="$4" gap="$2">
                    <XStack
                        flexDirection="row"
                        items="center"
                    >
                        <ClipboardList />
                        <H5 ml="$2" theme="light">
                            Service Request
                        </H5>
                    </XStack>
                    <Text>
                        Request assistance and
                        services tailored to your
                        needs. Our team will
                        review your request and
                        get back to you soon.
                    </Text>
                </YStack>
                <YStack p="$4" gap="$4">
                    <form.AppField
                        name="service_type_id"
                        children={(field) => (
                            <field.ServiceTypeField />
                        )}
                    />
                    <XStack gap="$3">
                        <form.AppField
                            name="service_date"
                            children={(field) => (
                                <field.DateTimeSectionField />
                            )}
                        />
                        <form.AppField
                            name="service_time"
                            children={(field) => (
                                <field.DateTimeSectionField />
                            )}
                        />
                    </XStack>
                    <form.AppField
                        name="message"
                        children={(field) => (
                            <field.ServiceDetailsField />
                        )}
                    />
                    <form.AppForm>
                        <form.Submit />
                    </form.AppForm>
                </YStack>
            </Card>
        </KeyboardAvoidingView>
    );
};

export default ServiceAssistanceForm;
