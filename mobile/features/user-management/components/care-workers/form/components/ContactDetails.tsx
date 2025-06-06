import { FormSectionProps } from "features/user-management/components/care-workers/form/types";
import { Input, Label, XStack, YStack } from "tamagui";

export function ContactDetails({ formData, setFormData }: FormSectionProps) {
    return (
        <YStack gap="$4">
            <Label size="$6" fontWeight="bold">
                Contact Information
            </Label>

            <YStack gap="$3">
                <Label htmlFor="personalEmail" color="$red10">
                    Personal Email Address *
                </Label>
                <Input
                    id="personalEmail"
                    placeholder="Enter personal email"
                    keyboardType="email-address"
                    value={formData.personalEmail}
                    onChangeText={(text) => setFormData({ ...formData, personalEmail: text })}
                />
            </YStack>

            <XStack gap="$3">
                <YStack flex={1}>
                    <Label htmlFor="mobileNumber" color="$red10">
                        Mobile Number *
                    </Label>
                    <XStack gap="$2">
                        <Input value="+63" width={50} editable={false} />
                        <Input
                            flex={1}
                            id="mobileNumber"
                            placeholder="Enter mobile number"
                            keyboardType="phone-pad"
                            value={formData.mobileNumber}
                            onChangeText={(text) =>
                                setFormData({ ...formData, mobileNumber: text })
                            }
                        />
                    </XStack>
                </YStack>

                <YStack flex={1}>
                    <Label htmlFor="landlineNumber">Landline Number</Label>
                    <Input
                        id="landlineNumber"
                        placeholder="Enter landline number"
                        keyboardType="phone-pad"
                        value={formData.landlineNumber}
                        onChangeText={(text) => setFormData({ ...formData, landlineNumber: text })}
                    />
                </YStack>
            </XStack>
        </YStack>
    );
}
