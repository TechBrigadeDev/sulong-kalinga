import { YStack, XStack, Label, Input } from 'tamagui'
import { FormSectionProps } from '../types'

export function ContactDetails({ formData, setFormData }: FormSectionProps) {
  return (
    <YStack space="$4">
      <Label size="$6" fontWeight="bold">Contact Information</Label>
      
      <YStack space="$3">
        <Label htmlFor="personalEmail" color="$red10">Personal Email Address *</Label>
        <Input
          id="personalEmail"
          placeholder="Enter personal email"
          keyboardType="email-address"
          value={formData.personalEmail}
          onChangeText={(text) => setFormData({ ...formData, personalEmail: text })}
        />
      </YStack>

      <XStack space="$3">
        <YStack flex={1}>
          <Label htmlFor="mobileNumber" color="$red10">Mobile Number *</Label>
          <XStack space="$2">
            <Input value="+63" width={50} editable={false} />
            <Input
              flex={1}
              id="mobileNumber"
              placeholder="Enter mobile number"
              keyboardType="phone-pad"
              value={formData.mobileNumber}
              onChangeText={(text) => setFormData({ ...formData, mobileNumber: text })}
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
  )
}
