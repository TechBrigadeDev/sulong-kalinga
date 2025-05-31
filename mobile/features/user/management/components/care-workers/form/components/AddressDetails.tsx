import { YStack, Label, TextArea } from 'tamagui'
import { FormSectionProps } from '../types'

export function AddressDetails({ formData, setFormData }: FormSectionProps) {
  return (
    <YStack space="$3">
      <Label size="$6" fontWeight="bold">Current Address</Label>
      <Label htmlFor="address" color="$red10">House No., Street, Subdivision, Barangay, City, Province *</Label>
      <TextArea
        id="address"
        placeholder="Enter complete current address"
        value={formData.address}
        onChangeText={(text) => setFormData({ ...formData, address: text })}
        numberOfLines={3}
      />
    </YStack>
  )
}
