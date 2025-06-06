import { FormSectionProps } from "features/user-management/components/care-workers/form/types";
import { Label, TextArea, YStack } from "tamagui";

export function AddressDetails({
    formData,
    setFormData,
}: FormSectionProps) {
    return (
        <YStack gap="$3">
            <Label size="$6" fontWeight="bold">
                Current Address
            </Label>
            <Label
                htmlFor="address"
                color="$red10"
            >
                House No., Street, Subdivision,
                Barangay, City, Province *
            </Label>
            <TextArea
                id="address"
                placeholder="Enter complete current address"
                value={formData.address}
                onChangeText={(text) =>
                    setFormData({
                        ...formData,
                        address: text,
                    })
                }
                numberOfLines={3}
            />
        </YStack>
    );
}
