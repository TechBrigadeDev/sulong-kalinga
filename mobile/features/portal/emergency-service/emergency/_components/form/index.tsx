import {
    useEditEmergencyRequest,
    useEmergencyRequest,
} from "features/portal/emergency-service/emergency/hook";
import { EmergencyServiceFormProp } from "features/portal/emergency-service/emergency/interface";
import { useEmergencyServiceStore } from "features/portal/emergency-service/store";
import { TriangleAlert } from "lucide-react-native";
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
    emergencyFormOpts,
    useEmergencyForm,
} from "./form";
import { IEmergencyForm } from "./interface";

const EmergencyAssistanceForm = ({
    onSubmitSuccess,
}: EmergencyServiceFormProp) => {
    const {
        mutateAsync: submitEmergencyRequest,
    } = useEmergencyRequest();
    const { mutateAsync: editEmergencyRequest } =
        useEditEmergencyRequest();

    const store = useEmergencyServiceStore();

    const form = useEmergencyForm({
        ...emergencyFormOpts,
        defaultValues: {
            message: "",
            emergency_type_id: "",
        } as IEmergencyForm,
        onSubmit: async ({ value, formApi }) => {
            const request =
                store.getState().request;

            try {
                if (request && request.id) {
                    await editEmergencyRequest({
                        id: request.id.toString(),
                        data: value,
                    });
                } else {
                    await submitEmergencyRequest(
                        value,
                    );
                }
                onSubmitSuccess?.();

                formApi.reset({
                    message: "",
                    emergency_type_id: "",
                });
            } catch (error) {
                console.error(
                    "Error submitting emergency request:",
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
        if (request?.type === "service") {
            return;
        }

        if (request) {
            console.log(
                "Emergency form: Populating form with request data:",
                request,
            );
            form.reset({
                emergency_type_id:
                    request.emergency_type_id.toString(),
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
                        <TriangleAlert />
                        <H5 ml="$2" theme="light">
                            Emergency Assistance
                        </H5>
                    </XStack>
                    <Text>
                        Immediate help when you
                        need it most. Our team
                        will respond to you as
                        soon as we can.
                    </Text>
                </YStack>
                <YStack p="$4" gap="$2">
                    <form.AppField
                        name="emergency_type_id"
                        children={(field) => (
                            <field.TypeField />
                        )}
                    />
                    <form.AppField
                        name="message"
                        children={(field) => (
                            <field.DescriptionField />
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

export default EmergencyAssistanceForm;
