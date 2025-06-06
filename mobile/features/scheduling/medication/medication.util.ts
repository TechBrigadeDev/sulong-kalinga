import {
    IGroupedMedicationSchedule,
    IMedicationSchedule,
} from "./medication.type";

export const groupMedicationScheduleByBeneficiary =
    (
        schedules: IMedicationSchedule[],
    ): IGroupedMedicationSchedule[] => {
        const grouped: Record<
            number,
            IGroupedMedicationSchedule
        > = {};

        schedules.forEach((schedule) => {
            const beneficiaryId =
                schedule.beneficiary_id;

            if (!grouped[beneficiaryId]) {
                grouped[beneficiaryId] = {
                    beneficiary:
                        schedule.beneficiary,
                    medication_schedules: [],
                };
            }

            grouped[
                beneficiaryId
            ].medication_schedules.push(schedule);
        });

        return Object.values(grouped);
    };
