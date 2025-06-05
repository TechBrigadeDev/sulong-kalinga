import TabScroll from "components/tabs/TabScroll";
import { Text, YStack } from "tamagui";
import { type z } from "zod";

import { adminSchema } from "~/features/user-management/schema/admin";

import AdminHeader from "./components/AdminHeader";
import ContactInformation from "./components/ContactInformation";
import Documents from "./components/Documents";
import GovernmentIDs from "./components/GovernmentIDs";
import PersonalDetails from "./components/PersonalDetails";

type IAdmin = z.infer<typeof adminSchema>;

interface AdminDetailProps {
    admin: IAdmin;
}

function AdminDetail({ admin }: AdminDetailProps) {
    if (!admin) {
        return (
            <YStack style={{ padding: 16, flex: 1, alignItems: 'center', justifyContent: 'center' }}>
                <Text>Administrator data is not available</Text>
            </YStack>
        );
    }

    return (
        <TabScroll>
            <YStack p="$4" gap="$4">
                <AdminHeader admin={admin} />
                <PersonalDetails admin={admin} />
                <ContactInformation admin={admin} />
                <Documents admin={admin} />
                <GovernmentIDs admin={admin} />
            </YStack>
        </TabScroll>
    );
}

export default AdminDetail;
