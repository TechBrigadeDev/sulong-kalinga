import {
    createContext,
    useContext,
    useRef,
} from "react";
import { createStore, StoreApi } from "zustand";

import {
    ICurrentEmergencyServiceForm,
    IEmergencyServiceRequest,
} from "./type";

interface State {
    request: IEmergencyServiceRequest | null;
    setRequest: (
        request: IEmergencyServiceRequest | null,
    ) => void;

    currentEmergencyServiceForm: ICurrentEmergencyServiceForm;
    setCurrentEmergencyServiceForm: (
        form: ICurrentEmergencyServiceForm,
    ) => void;
}

export const EmergencyServiceContext =
    createContext<StoreApi<State> | null>(null);

const store: StoreApi<State> = createStore(
    (set) => ({
        request: null,
        setRequest: (request) => {
            console.log(
                "Store: Setting emergency service request to:",
                request,
            );
            set({ request });
        },
        currentEmergencyServiceForm: "emergency",
        setCurrentEmergencyServiceForm: (
            form,
        ) => {
            console.log(
                "Store: Setting current emergency service form to:",
                form,
            );
            set({
                currentEmergencyServiceForm: form,
            });
        },
    }),
);

export const EmergencyServiceProvider = ({
    children,
}: {
    children: React.ReactNode;
}) => {
    const ref = useRef<StoreApi<State> | null>(
        null,
    );
    if (ref.current === null) {
        ref.current = store;
    }

    return (
        <EmergencyServiceContext.Provider
            value={ref.current}
        >
            {children}
        </EmergencyServiceContext.Provider>
    );
};

export const useEmergencyServiceStore = () => {
    const store = useContext(
        EmergencyServiceContext,
    );

    if (!store) {
        throw new Error(
            "useEmergencyServiceStore must be used within a EmergencyServiceProvider",
        );
    }

    return store;
};
