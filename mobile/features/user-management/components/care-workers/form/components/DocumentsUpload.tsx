import { Ionicons } from "@expo/vector-icons";
import * as DocumentPicker from "expo-document-picker";
import * as ImagePicker from "expo-image-picker";
import { FormSectionProps } from "features/user-management/components/care-workers/form/types";
import { Button, Input, Label, XStack, YStack } from "tamagui";

export function DocumentsUpload({ formData, setFormData }: FormSectionProps) {
    const handleUploadPhoto = async () => {
        const result = await ImagePicker.launchImageLibraryAsync({
            mediaTypes: ImagePicker.MediaTypeOptions.Images,
            allowsEditing: true,
            aspect: [1, 1],
            quality: 1,
        });
        // Handle the result
    };

    const handleUploadDocument = async () => {
        const result = await DocumentPicker.getDocumentAsync({
            type: "application/pdf",
            copyToCacheDirectory: true,
        });
        // Handle the result
    };

    return (
        <YStack gap="$4">
            <Label size="$6" fontWeight="bold">
                Documents Upload
            </Label>

            <XStack gap="$3">
                <YStack flex={1}>
                    <Label>Care Worker Photo</Label>
                    <Button
                        icon={<Ionicons name="image-outline" size={20} />}
                        onPress={handleUploadPhoto}
                        theme="green"
                    >
                        Choose Photo
                    </Button>
                    <Label size="$2" color="$red10">
                        Maximum file size: 7MB
                    </Label>
                </YStack>

                <YStack flex={1}>
                    <Label>Government Issued ID</Label>
                    <Button
                        icon={<Ionicons name="document-outline" size={20} />}
                        onPress={handleUploadDocument}
                        theme="green"
                    >
                        Choose File
                    </Button>
                    <Label size="$2" color="$red10">
                        Maximum file size: 7MB
                    </Label>
                </YStack>
            </XStack>

            <XStack gap="$3">
                <YStack flex={1}>
                    <Label>Resume / CV</Label>
                    <Button
                        icon={<Ionicons name="document-outline" size={20} />}
                        onPress={handleUploadDocument}
                        theme="green"
                    >
                        Choose File
                    </Button>
                    <Label size="$2" color="$red10">
                        Maximum file size: 5MB
                    </Label>
                </YStack>
            </XStack>

            <XStack gap="$3">
                <YStack flex={1}>
                    <Label htmlFor="sssId">SSS ID</Label>
                    <Input
                        id="sssId"
                        placeholder="Enter SSS ID"
                        value={formData.sssId}
                        onChangeText={(text) => setFormData({ ...formData, sssId: text })}
                    />
                </YStack>

                <YStack flex={1}>
                    <Label htmlFor="philhealthId">PhilHealth ID</Label>
                    <Input
                        id="philhealthId"
                        placeholder="Enter PhilHealth ID"
                        value={formData.philhealthId}
                        onChangeText={(text) => setFormData({ ...formData, philhealthId: text })}
                    />
                </YStack>

                <YStack flex={1}>
                    <Label htmlFor="pagibigId">Pag-Ibig ID</Label>
                    <Input
                        id="pagibigId"
                        placeholder="Enter Pag-Ibig ID"
                        value={formData.pagibigId}
                        onChangeText={(text) => setFormData({ ...formData, pagibigId: text })}
                    />
                </YStack>
            </XStack>
        </YStack>
    );
}
