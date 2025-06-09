import { IWCPRecord } from "features/records/type";
import { YStack } from "tamagui";

import Assessment from "./components/Assessment";
import { CareInterventions } from "./components/CareInterventions";
import { EvaluationRecommendations } from "./components/EvaluationRecommendations";
// Import components
import { PersonalDetails } from "./components/PersonalDetails";
import { VitalSigns } from "./components/VitalSigns";

interface Props {
    record: IWCPRecord;
}

const WCPRecordDetail = ({ record }: Props) => {
    return (
        <YStack
            rowGap="$4"
            style={{ padding: 16 }}
        >
            <PersonalDetails
                data={{
                    beneficiary:
                        record.beneficiary,
                    care_worker:
                        record.care_worker,
                    plan_date: record.date,
                }}
            />

            <Assessment
                assessment={record.assessment}
            />

            <VitalSigns
                vitalSigns={{
                    blood_pressure:
                        record.vital_signs
                            .blood_pressure,
                    temperature:
                        record.vital_signs
                            .body_temperature,
                    pulse: record.vital_signs.pulse_rate.toString(),
                    respiratory_rate:
                        record.vital_signs.respiratory_rate.toString(),
                }}
                photoUrl={record.photo_url}
            />

            <EvaluationRecommendations
                evaluationRecommendations={
                    record.evaluation_recommendations
                }
            />

            <CareInterventions
                interventions={
                    record.interventions
                }
            />
        </YStack>
    );
};

export default WCPRecordDetail;
