import { EmergencyServiceFormProp } from "features/portal/emergency-service/emergency/interface";
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

import DateTimeSection from "./components/DateTimeSection";
import ServiceDetails from "./components/ServiceDetails";
import ServiceType from "./components/ServiceType";
import SubmitServiceRequest from "./components/SubmitServiceRequest";
import { useServiceRequestForm } from "./form";

const ServiceForm = ({
    ref,
}: EmergencyServiceFormProp) => {
    const form = useServiceRequestForm();

    const store = useEmergencyServiceStore();
    useEffect(() => {
        if (!form || !store) {
            return;
        }

        store.subscribe((state) => {
            if (
                state.request &&
                state.request.type === "service"
            ) {
                const request = state.request;

                form.reset({
                    message: request.description,
                    service_type_id:
                        request.service_type_id.toString(),
                    service_date:
                        request.service_date,
                    service_time:
                        request.service_time,
                });
            } else if (form && !state.request) {
                form.reset();
            }
            return;
        });
    }, [store, form]);
    return (
        <KeyboardAvoidingView
            style={{ flex: 1 }}
            behavior="padding"
            keyboardVerticalOffset={100}
        >
            <Card
                marginBottom="$2"
                borderRadius={8}
                borderWidth={1}
                elevate
                gap="$4"
                ref={ref}
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
                    <ServiceType />
                    <DateTimeSection />
                    <ServiceDetails />
                    <SubmitServiceRequest />
                </YStack>
            </Card>
        </KeyboardAvoidingView>
    );
};

export default ServiceForm;
