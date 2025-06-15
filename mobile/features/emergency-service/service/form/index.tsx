import { ClipboardList } from "lucide-react-native";
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
import { ServiceRequestForm } from "./form";

const ServiceForm = () => {
    return (
        <KeyboardAvoidingView
            style={{ flex: 1 }}
            behavior="padding"
            keyboardVerticalOffset={100}
        >
            <ServiceRequestForm>
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
                            <H5
                                ml="$2"
                                theme="light"
                            >
                                Service Request
                            </H5>
                        </XStack>
                        <Text>
                            Request assistance and
                            services tailored to
                            your needs. Our team
                            will review your
                            request and get back
                            to you soon.
                        </Text>
                    </YStack>
                    <YStack p="$4" gap="$4">
                        <ServiceType />
                        <DateTimeSection />
                        <ServiceDetails />
                        <SubmitServiceRequest />
                    </YStack>
                </Card>
            </ServiceRequestForm>
        </KeyboardAvoidingView>
    );
};

export default ServiceForm;
