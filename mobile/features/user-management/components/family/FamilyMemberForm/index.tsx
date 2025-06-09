import { Ionicons } from "@expo/vector-icons";
import { useRouter } from "expo-router";
import { IFamilyMember } from "features/user-management/management.type";
import { useState } from "react";
import {
    Button,
    Form,
    ScrollView,
    YStack,
} from "tamagui";

import AddressSection from "./AddressSection";
import PersonalDetailsSection from "./PersonalDetailsSection";
import RelationSection from "./RelationSection";

interface Props {
    familyMember?: Partial<IFamilyMember>;
    onSubmit?: (
        data: Partial<IFamilyMember>,
    ) => void;
}

const FamilyMemberForm = ({
    familyMember,
    onSubmit,
}: Props) => {
    const router = useRouter();
    const [form, setForm] = useState<
        Partial<IFamilyMember>
    >(familyMember || {});

    const handleFieldChange = (
        key: keyof IFamilyMember,
        value: unknown,
    ) => {
        setForm(
            (prev: Partial<IFamilyMember>) => ({
                ...prev,
                [key]: value,
            }),
        );
    };

    const handleSubmit = () => {
        if (onSubmit) {
            onSubmit(form);
        }
        router.back();
    };

    return (
        <ScrollView>
            <Form onSubmit={handleSubmit}>
                <YStack gap="$4" p="$4">
                    <PersonalDetailsSection
                        data={form}
                        onChange={
                            handleFieldChange
                        }
                    />
                    <AddressSection
                        data={form}
                        onChange={
                            handleFieldChange
                        }
                    />
                    <RelationSection
                        data={form}
                        onChange={
                            handleFieldChange
                        }
                    />
                    <Button
                        theme="green"
                        size="$5"
                        icon={
                            <Ionicons
                                name="save-outline"
                                size={20}
                                color="white"
                            />
                        }
                        onPress={handleSubmit}
                    >
                        {familyMember
                            ? "Save Changes"
                            : "Add Family Member"}
                    </Button>
                </YStack>
            </Form>
        </ScrollView>
    );
};

export default FamilyMemberForm;
