import { Ionicons } from "@expo/vector-icons";
import { useState } from "react";
import { Button, Form, ScrollView, YStack } from "tamagui";

import { AccountRegistration } from "./components/AccountRegistration";
import { AddressDetails } from "./components/AddressDetails";
import { ContactDetails } from "./components/ContactDetails";
import { DocumentsUpload } from "./components/DocumentsUpload";
import { PersonalDetails } from "./components/PersonalDetails";
import { CareWorkerFormData } from "./types";

export default function CareWorkerForm() {
    const [formData, setFormData] = useState<CareWorkerFormData>({
        firstName: "",
        lastName: "",
        birthday: new Date(),
        gender: "",
        civilStatus: "",
        religion: "",
        nationality: "",
        educationalBackground: "",
        address: "",
        personalEmail: "",
        mobileNumber: "",
        landlineNumber: "",
        sssId: "",
        philhealthId: "",
        pagibigId: "",
        workEmail: "",
        password: "",
        confirmPassword: "",
        municipality: "",
        careManager: "",
    });

    return (
        <ScrollView>
            <Form onSubmit={() => console.log(formData)}>
                <YStack gap="$4" p="$4">
                    <PersonalDetails formData={formData} setFormData={setFormData} />
                    <AddressDetails formData={formData} setFormData={setFormData} />
                    <ContactDetails formData={formData} setFormData={setFormData} />
                    <DocumentsUpload formData={formData} setFormData={setFormData} />
                    <AccountRegistration formData={formData} setFormData={setFormData} />

                    <Button
                        theme="green"
                        size="$5"
                        icon={<Ionicons name="save-outline" size={20} color="white" />}
                        onPress={() => console.log(formData)}
                    >
                        Save Care Worker
                    </Button>
                </YStack>
            </Form>
        </ScrollView>
    );
}
