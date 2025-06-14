import { Href, useRouter } from "expo-router";
import {
    createContext,
    PropsWithChildren,
    useContext,
    useEffect,
    useRef,
} from "react";
import { createStore, StoreApi } from "zustand";

interface State {
    dataUri: string;
    setDataUri: (dataUri: string) => void;

    path: Href | null;
    setPath: (path: Href | null) => void;

    callBack: null | ((dataUri: string) => void);
    setCallBack: (
        callBack:
            | null
            | ((dataUri: string) => void),
    ) => void;
}

export const DrawingContext =
    createContext<StoreApi<State> | null>(null);

const store: StoreApi<State> = createStore(
    (set) => ({
        dataUri: "",
        setDataUri: (dataUri: string) =>
            set(() => ({
                dataUri,
            })),
        path: null,
        setPath: (path) =>
            set({
                path,
            }),
        callBack: null,
        setCallBack: (callBack) =>
            set({
                callBack,
            }),
    }),
);

export const DrawingProvider = ({
    children,
}: PropsWithChildren) => {
    const ref = useRef<StoreApi<State> | null>(
        null,
    );
    if (ref.current === null) {
        ref.current = store;
    }

    return (
        <DrawingContext.Provider
            value={ref.current}
        >
            {children}
        </DrawingContext.Provider>
    );
};

export const useDrawing = () => {
    const store = useContext(DrawingContext);
    if (!store) {
        throw new Error(
            "useDrawing must be used within a DrawingProvider",
        );
    }

    const { push } = useRouter();
    const { setCallBack, setPath } =
        store.getState();

    const onDraw = (
        path: Href,
        callback: (dataUri: string) => void,
    ) => {
        setCallBack(callback);
        setPath(path);

        push("/(modals)/signature");
    };

    useEffect(() => {
        return () => {
            setCallBack(null);
            setPath(null);
        };
    }, [setCallBack, setPath]);

    return {
        onDraw,
    };
};
