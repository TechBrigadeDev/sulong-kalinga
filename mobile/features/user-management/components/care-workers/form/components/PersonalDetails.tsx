import { Ionicons } from "@expo/vector-icons";
import { DateTimePickerAndroid, DateTimePickerEvent } from "@react-native-community/datetimepicker";
import { FormSectionProps } from "features/user-management/components/care-workers/form/types";
import { Button, Input, Label, Select, XStack, YStack } from "tamagui";

const genderOptions = [
    { name: "Male", value: "male" },
    { name: "Female", value: "female" },
    { name: "Other", value: "other" },
];

const civilStatusOptions = [
    { name: "Single", value: "single" },
    { name: "Married", value: "married" },
    { name: "Widowed", value: "widowed" },
    { name: "Divorced", value: "divorced" },
];

const educationalBackgroundOptions = [
    { name: "High School", value: "high_school" },
    { name: "College", value: "college" },
    { name: "Vocational", value: "vocational" },
    { name: "Post Graduate", value: "post_graduate" },
];

export function PersonalDetails({ formData, setFormData }: FormSectionProps) {
    const handleDatePicker = () => {
        DateTimePickerAndroid.open({
            value: formData.birthday,
            onChange: (event: DateTimePickerEvent, date?: Date) => {
                if (date) setFormData({ ...formData, birthday: date });
            },
            mode: "date",
        });
    };

    return (
        <YStack gap="$4">
            <Label size="$6" fontWeight="bold">
                Personal Details
            </Label>

            <XStack gap="$3">
                <YStack flex={1}>
                    <Label htmlFor="firstName" color="$red10">
                        First Name *
                    </Label>
                    <Input
                        id="firstName"
                        placeholder="Enter first name"
                        value={formData.firstName}
                        onChangeText={(text) => setFormData({ ...formData, firstName: text })}
                    />
                </YStack>

                <YStack flex={1}>
                    <Label htmlFor="lastName" color="$red10">
                        Last Name *
                    </Label>
                    <Input
                        id="lastName"
                        placeholder="Enter last name"
                        value={formData.lastName}
                        onChangeText={(text) => setFormData({ ...formData, lastName: text })}
                    />
                </YStack>
            </XStack>

            <XStack gap="$3">
                <YStack flex={1}>
                    <Label htmlFor="birthday" color="$red10">
                        Birthday *
                    </Label>
                    <Button
                        icon={<Ionicons name="calendar-outline" size={20} />}
                        onPress={handleDatePicker}
                    >
                        {formData.birthday.toLocaleDateString()}
                    </Button>
                </YStack>

                <YStack flex={1}>
                    <Label htmlFor="gender">Gender</Label>
                    <Select
                        id="gender"
                        value={formData.gender}
                        onValueChange={(value) => setFormData({ ...formData, gender: value })}
                    >
                        <Select.Trigger>
                            <Select.Value placeholder="Select gender" />
                        </Select.Trigger>
                        <Select.Content>
                            <Select.ScrollUpButton />
                            <Select.Viewport>
                                {genderOptions.map((option, index) => (
                                    <Select.Item
                                        key={option.value}
                                        value={option.value}
                                        index={index}
                                    >
                                        <Select.ItemText>{option.name}</Select.ItemText>
                                    </Select.Item>
                                ))}
                            </Select.Viewport>
                            <Select.ScrollDownButton />
                        </Select.Content>
                    </Select>
                </YStack>
            </XStack>

            <XStack gap="$3">
                <YStack flex={1}>
                    <Label htmlFor="civilStatus">Civil Status</Label>
                    <Select
                        id="civilStatus"
                        value={formData.civilStatus}
                        onValueChange={(value) => setFormData({ ...formData, civilStatus: value })}
                    >
                        <Select.Trigger>
                            <Select.Value placeholder="Select civil status" />
                        </Select.Trigger>
                        <Select.Content>
                            <Select.ScrollUpButton />
                            <Select.Viewport>
                                {civilStatusOptions.map((option, index) => (
                                    <Select.Item
                                        key={option.value}
                                        value={option.value}
                                        index={index}
                                    >
                                        <Select.ItemText>{option.name}</Select.ItemText>
                                    </Select.Item>
                                ))}
                            </Select.Viewport>
                            <Select.ScrollDownButton />
                        </Select.Content>
                    </Select>
                </YStack>

                <YStack flex={1}>
                    <Label htmlFor="religion">Religion</Label>
                    <Input
                        id="religion"
                        placeholder="Enter religion"
                        value={formData.religion}
                        onChangeText={(text) => setFormData({ ...formData, religion: text })}
                    />
                </YStack>
            </XStack>

            <XStack gap="$3">
                <YStack flex={1}>
                    <Label htmlFor="nationality">Nationality</Label>
                    <Input
                        id="nationality"
                        placeholder="Enter nationality"
                        value={formData.nationality}
                        onChangeText={(text) => setFormData({ ...formData, nationality: text })}
                    />
                </YStack>

                <YStack flex={1}>
                    <Label htmlFor="educationalBackground">Educational Background</Label>
                    <Select
                        id="educationalBackground"
                        value={formData.educationalBackground}
                        onValueChange={(value) =>
                            setFormData({ ...formData, educationalBackground: value })
                        }
                    >
                        <Select.Trigger>
                            <Select.Value placeholder="Select education" />
                        </Select.Trigger>
                        <Select.Content>
                            <Select.ScrollUpButton />
                            <Select.Viewport>
                                {educationalBackgroundOptions.map((option, index) => (
                                    <Select.Item
                                        key={option.value}
                                        value={option.value}
                                        index={index}
                                    >
                                        <Select.ItemText>{option.name}</Select.ItemText>
                                    </Select.Item>
                                ))}
                            </Select.Viewport>
                            <Select.ScrollDownButton />
                        </Select.Content>
                    </Select>
                </YStack>
            </XStack>
        </YStack>
    );
}
