import { TriangleAlert } from "lucide-react-native";
import { KeyboardAvoidingView } from "react-native";
import {
    Card,
    H5,
    Text,
    XStack,
    YStack,
} from "tamagui";

import EmergencyDescription from "./_components/Description";
import EmergencyType from "./_components/EmergencyType";
import SubmitEmergency from "./_components/Submit";
import { EmergencyForm } from "./form";

const EmergencyAssistanceForm = () => {
    return (
        <KeyboardAvoidingView
            style={{ flex: 1 }}
            behavior="padding"
            keyboardVerticalOffset={100}
        >
            <EmergencyForm>
                <Card
                    marginBottom="$2"
                    borderRadius={8}
                    borderWidth={1}
                    elevate
                    marginHorizontal="$4"
                    gap="$4"
                >
                    <YStack p="$4" gap="$2">
                        <XStack
                            flexDirection="row"
                            items="center"
                        >
                            <TriangleAlert />
                            <H5
                                ml="$2"
                                theme="light"
                            >
                                Emergency
                                Assistance
                            </H5>
                        </XStack>
                        <Text>
                            Immediate help when
                            you need it most. Our
                            team will respond to
                            you as soon as we can.
                        </Text>
                    </YStack>
                    <YStack p="$4" gap="$2">
                        <EmergencyType />
                        <EmergencyDescription />
                        <SubmitEmergency />
                    </YStack>
                </Card>
            </EmergencyForm>
        </KeyboardAvoidingView>
    );
};

export default EmergencyAssistanceForm;
