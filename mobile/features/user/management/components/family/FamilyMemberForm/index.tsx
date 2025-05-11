import { Button, Form, ScrollView, XStack, YStack } from "tamagui";
import { IFamilyMember } from "../../../../user.schema";
import { useState } from "react";
import { useRouter } from "expo-router";
import PersonalDetailsSection from "./PersonalDetailsSection";
import AddressSection from "./AddressSection";
import RelationSection from "./RelationSection";

interface Props {
    familyMember?: Partial<IFamilyMember>;
    onSubmit?: (data: Partial<IFamilyMember>) => void;
}

const FamilyMemberForm = ({ familyMember, onSubmit }: Props) => {
    const router = useRouter();
    const [form, setForm] = useState<Partial<IFamilyMember>>(familyMember || {});

    const handleFieldChange = (key: keyof IFamilyMember, value: any) => {
        setForm(prev => ({
            ...prev,
            [key]: value
        }));
    };

    const handleSubmit = () => {
        if (onSubmit) {
            onSubmit(form);
        }
        router.back();
    };

    return (
        <ScrollView padding="$4">
            <Form onSubmit={handleSubmit}>
                <YStack space="$4">
                    <PersonalDetailsSection
                        data={form}
                        onChange={handleFieldChange}
                    />
                    <AddressSection
                        data={form}
                        onChange={handleFieldChange}
                    />
                    <RelationSection
                        data={form}
                        onChange={handleFieldChange}
                    />
                    <XStack paddingVertical="$4" justifyContent="flex-end" space="$4">
                        <Button
                            size="$3"
                            theme="dark"
                            onPress={handleSubmit}
                        >
                            {familyMember ? "Save Changes" : "Add Family Member"}
                        </Button>
                    </XStack>
                </YStack>
            </Form>
        </ScrollView>
    );
};

export default FamilyMemberForm;
