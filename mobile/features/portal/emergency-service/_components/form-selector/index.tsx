import EmergencyAssistanceForm from "features/portal/emergency-service/emergency/_components/form";
import { EmergencyServiceFormProp } from "features/portal/emergency-service/emergency/interface";
import ServiceForm from "features/portal/emergency-service/service/form";
import { useEmergencyServiceStore } from "features/portal/emergency-service/store";
import { ICurrentEmergencyServiceForm } from "features/portal/emergency-service/type";
import { ReactNode, useState } from "react";
import {
    Separator,
    SizableText,
    Tabs,
    TabsContentProps,
} from "tamagui";

const tabs: {
    value: string;
    label: string;
    form: ReactNode;
}[] = [
    {
        value: "emergency",
        label: "Emergency",
        form: <EmergencyAssistanceForm />,
    },
    {
        value: "service",
        label: "Service Request",
        form: <ServiceForm />,
    },
];

const EmergencyServiceFormSelector = ({
    ref,
}: EmergencyServiceFormProp) => {
    const store = useEmergencyServiceStore();

    const [form, setForm] =
        useState<ICurrentEmergencyServiceForm>(
            (store.getState().request
                ?.type as ICurrentEmergencyServiceForm) ||
                "emergency",
        );

    store.subscribe((state) => {
        if (state.request?.type) {
            setForm(
                state.request
                    .type as ICurrentEmergencyServiceForm,
            );
        }
    });

    return (
        <Tabs
            value={form}
            onValueChange={(value) => {
                setForm(
                    value as ICurrentEmergencyServiceForm,
                );
            }}
            orientation="horizontal"
            flexDirection="column"
        >
            <Tabs.List
                disablePassBorderRadius
                radiused={false}
                gap={"$2"}
                marginBlockEnd={"$4"}
            >
                {tabs.map((tab) => (
                    <Tabs.Tab
                        key={tab.value}
                        focusStyle={{
                            backgroundColor:
                                "$color3",
                        }}
                        flex={1}
                        value={tab.value}
                        onPress={() => {
                            store
                                .getState()
                                .setRequest(null);
                        }}
                    >
                        <SizableText
                            fontFamily="$body"
                            text="center"
                        >
                            {tab.label}
                        </SizableText>
                    </Tabs.Tab>
                ))}
            </Tabs.List>
            <Separator />
            {tabs.map((tab) => (
                <TabsContent
                    key={tab.value}
                    value={tab.value}
                >
                    {tab.form}
                </TabsContent>
            ))}
        </Tabs>
    );
};

const TabsContent = (props: TabsContentProps) => {
    return (
        <Tabs.Content
            // items="center"
            // content="center"
            {...props}
        >
            {props.children}
        </Tabs.Content>
    );
};

export default EmergencyServiceFormSelector;
