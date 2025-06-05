import TabScroll from "components/tabs/TabScroll";
import { YStack } from "tamagui";
import { type z } from "zod";
import { careWorkerSchema } from "~/features/user-management/schema/care-worker";
import CareWorkerHeader from "./components/CareWorkerHeader";
import ContactInformation from "./components/ContactInformation";
import Documents from "./components/Documents";
import GovernmentIDs from "./components/GovernmentIDs";
import PersonalDetails from "./components/PersonalDetails";
import WorkInformation from "./components/WorkInformation";

type ICareWorker = z.infer<typeof careWorkerSchema>;

interface CareWorkerDetailProps {
    careWorker: ICareWorker;
}

function CareWorkerDetail({ careWorker }: CareWorkerDetailProps) {
    return (
        <TabScroll>
            <YStack p="$4" gap="$4">
                <CareWorkerHeader careWorker={careWorker} />
                <PersonalDetails careWorker={careWorker} />
                <ContactInformation careWorker={careWorker} />
                <WorkInformation careWorker={careWorker} />
                <Documents careWorker={careWorker} />
                <GovernmentIDs careWorker={careWorker} />
            </YStack>
        </TabScroll>
    );
}

export default CareWorkerDetail;
