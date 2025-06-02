import { Card, H3, YStack, Input, XStack, Text } from "tamagui";
import { IBeneficiary } from "~/features/user/management/management.type";

interface Props {
    data?: Partial<IBeneficiary>;
    onChange?: (field: string | number | symbol, value: any) => void;
}

export const EmergencyContactSection = ({ 
    data = {}, 
    onChange = () => {} 
}: Props) => {
    return (
        <Card elevate>
            <Card.Header padded>
                <H3>Emergency Contact</H3>
            </Card.Header>
            <Card.Footer padded>
                <YStack space="$4">
                    <XStack space="$4">
                        <YStack flex={1}>
                            <Text>Contact Person Name *</Text>
                            <Input
                                value={data.emergency_contact_name}
                                onChangeText={(value) => onChange("emergency_contact_name", value)}
                                placeholder="Enter contact name"
                                autoCapitalize="words"
                            />
                        </YStack>
                        <YStack flex={1}>
                            <Text>Relationship *</Text>
                            <Input
                                value={data.emergency_contact_relation}
                                onChangeText={(value) => onChange("emergency_contact_relation", value)}
                                placeholder="Enter relationship"
                                autoCapitalize="words"
                            />
                        </YStack>
                    </XStack>

                    <XStack space="$4">
                        <YStack flex={1}>
                            <Text>Mobile Number *</Text>
                            <XStack space="$2" alignItems="center">
                                <Input
                                    flex={1}
                                    value={data.emergency_contact_mobile?.replace('+63', '')}
                                    onChangeText={(value) => onChange("emergency_contact_mobile", `+63${value}`)}
                                    placeholder="Enter mobile number"
                                    keyboardType="phone-pad"
                                />
                            </XStack>
                        </YStack>
                    </XStack>

                    <YStack>
                        <Text>Emergency Procedure</Text>
                        <Input
                            value={data.emergency_procedure}
                            onChangeText={(value) => onChange("emergency_procedure", value)}
                            placeholder="Enter emergency procedure"
                            multiline
                            numberOfLines={3}
                            textAlignVertical="top"
                        />
                    </YStack>
                </YStack>
            </Card.Footer>
        </Card>
    );
};
