import { IRecordDetail } from "features/records/interface";
import { create } from "zustand";

interface CarePlanFormStore {
    record: IRecordDetail | null;
    setRecord: (
        value: IRecordDetail | null,
    ) => void;
    currentStep: number;
    setCurrentStep: (step: number) => void;
    resetStep: () => void;
}

export const useCarePlanFormStore =
    create<CarePlanFormStore>((set) => ({
        record: null,
        setRecord: (record) =>
            set({
                record,
            }),
        currentStep: 0,
        setCurrentStep: (step: number) => {
            console.log(
                "🔄 Setting step to:",
                step,
            );
            set({ currentStep: step });
        },
        resetStep: () => {
            console.log("🔄 Resetting step to 0");
            set({ currentStep: 0 });
        },
    }));
