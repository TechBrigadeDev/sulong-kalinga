import { useWindowDimensions } from "react-native";
import SignatureCanvas from "react-native-signature-canvas";
import { Button, Dialog, XStack, YStack } from "tamagui";

import { useSignatureStore } from "./store";

export const SignatureDialog = () => {
    const { width } = useWindowDimensions();
    const { isOpen, title, signature, onSave, setIsOpen, setSignature, reset } =
        useSignatureStore();

    const handleOK = (signature: string) => {
        setSignature(signature);
        if (onSave) {
            onSave(signature);
        }
        handleClose();
    };

    const handleClose = () => {
        setIsOpen(false);
        reset();
    };

    const handleClear = () => {
        setSignature(null);
    };

    return (
        <Dialog modal open={isOpen} onOpenChange={setIsOpen}>
            <Dialog.Portal>
                <Dialog.Overlay
                    key="overlay"
                    animation="quick"
                    opacity={0.5}
                    enterStyle={{ opacity: 0 }}
                    exitStyle={{ opacity: 0 }}
                />
                <Dialog.Content
                    bordered
                    elevate
                    key="content"
                    animation={[
                        "quick",
                        {
                            opacity: {
                                overshootClamping: true,
                            },
                        },
                    ]}
                    enterStyle={{ x: 0, y: -20, opacity: 0, scale: 0.9 }}
                    exitStyle={{ x: 0, y: 10, opacity: 0, scale: 0.95 }}
                    width={width * 0.9}
                >
                    <Dialog.Title>{title}</Dialog.Title>
                    <YStack space>
                        <SignatureCanvas
                            onOK={handleOK}
                            onEmpty={handleClear}
                            descriptionText="Sign above"
                            clearText="Clear"
                            confirmText="Save"
                            webStyle={`.m-signature-pad--footer
                                        {display: none; margin: 0px;}
                                    .m-signature-pad--body {
                                        border: none;
                                    }`}
                        />
                        <XStack space justifyContent="flex-end">
                            <Button onPress={handleClose} theme="gray">
                                Cancel
                            </Button>
                            <Button onPress={handleClear} theme="red">
                                Clear
                            </Button>
                        </XStack>
                    </YStack>
                </Dialog.Content>
            </Dialog.Portal>
        </Dialog>
    );
};
