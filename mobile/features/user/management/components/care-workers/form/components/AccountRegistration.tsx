import { YStack, XStack, Label, Input, Select } from 'tamagui'
import { FormSectionProps } from '../types'

export function AccountRegistration({ formData, setFormData }: FormSectionProps) {
  return (
    <YStack space="$4">
      <Label size="$6" fontWeight="bold">Care Worker Account Registration</Label>
      
      <YStack space="$3">
        <Label htmlFor="workEmail" color="$red10">Work Email Address *</Label>
        <Input
          id="workEmail"
          placeholder="Enter work email"
          keyboardType="email-address"
          value={formData.workEmail}
          onChangeText={(text) => setFormData({ ...formData, workEmail: text })}
        />
      </YStack>

      <XStack space="$3">
        <YStack flex={1}>
          <Label htmlFor="password" color="$red10">Password *</Label>
          <Input
            id="password"
            placeholder="Enter password"
            secureTextEntry
            value={formData.password}
            onChangeText={(text) => setFormData({ ...formData, password: text })}
          />
        </YStack>

        <YStack flex={1}>
          <Label htmlFor="confirmPassword" color="$red10">Confirm Password *</Label>
          <Input
            id="confirmPassword"
            placeholder="Confirm password"
            secureTextEntry
            value={formData.confirmPassword}
            onChangeText={(text) => setFormData({ ...formData, confirmPassword: text })}
          />
        </YStack>
      </XStack>

      <XStack space="$3">
        <YStack flex={1}>
          <Label htmlFor="municipality" color="$red10">Municipality *</Label>
          <Select
            id="municipality"
            value={formData.municipality}
            onValueChange={(value) => setFormData({ ...formData, municipality: value })}
          >
            <Select.Trigger>
              <Select.Value placeholder="Select municipality" />
            </Select.Trigger>
            <Select.Content>
              <Select.ScrollUpButton />
              <Select.Viewport>
                <Select.Item value="none" index={0}>
                  <Select.ItemText>Select municipality</Select.ItemText>
                </Select.Item>
              </Select.Viewport>
              <Select.ScrollDownButton />
            </Select.Content>
          </Select>
        </YStack>

        <YStack flex={1}>
          <Label htmlFor="careManager" color="$red10">Assigned Care Manager *</Label>
          <Select
            id="careManager"
            value={formData.careManager}
            onValueChange={(value) => setFormData({ ...formData, careManager: value })}
          >
            <Select.Trigger>
              <Select.Value placeholder="None (Unassigned)" />
            </Select.Trigger>
            <Select.Content>
              <Select.ScrollUpButton />
              <Select.Viewport>
                <Select.Item value="none" index={0}>
                  <Select.ItemText>None (Unassigned)</Select.ItemText>
                </Select.Item>
              </Select.Viewport>
              <Select.ScrollDownButton />
            </Select.Content>
          </Select>
        </YStack>
      </XStack>
    </YStack>
  )
}
