import EmergencyAssistanceForm from "features/portal/emergency-service/emergency/_components/form";
import { EmergencyServiceFormProp } from "features/portal/emergency-service/emergency/interface";
import ServiceForm from "features/portal/emergency-service/service/form";
import { useEmergencyServiceStore } from "features/portal/emergency-service/store";
import { ICurrentEmergencyServiceForm } from "features/portal/emergency-service/type";
import {
    ReactNode,
    useEffect,
    useState,
} from "react";
import {
    Separator,
    SizableText,
    Tabs,
    TabsContentProps,
    View,
} from "tamagui";

const tabs: {
    value: string;
    label: string;
    form: (
        prop: EmergencyServiceFormProp,
    ) => ReactNode;
}[] = [
    {
        value: "emergency",
        label: "Emergency",
        form: (prop) => (
            <EmergencyAssistanceForm {...prop} />
        ),
    },
    {
        value: "service",
        label: "Service Request",
        form: (prop) => <ServiceForm {...prop} />,
    },
];

const EmergencyServiceFormSelector = ({
    ref,
}: EmergencyServiceFormProp) => {
    const store = useEmergencyServiceStore();

    const [form, setForm] =
        useState<ICurrentEmergencyServiceForm>(
            "emergency",
        );

    useEffect(() => {
        store.subscribe((state) => {
            if (
                state.currentEmergencyServiceForm
            ) {
                setForm(
                    state.currentEmergencyServiceForm,
                );
            }
        });
    }, [store]);

    return (
        <Tabs
            value={form}
            orientation="horizontal"
            flexDirection="column"
            activationMode="manual"
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
                            console.log(
                                "Tabbing to:",
                                tab.value,
                            );
                            store.setState({
                                request: null,
                                currentEmergencyServiceForm:
                                    tab.value as ICurrentEmergencyServiceForm,
                            });
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
            <View ref={ref}>
                {tabs.map((tab) => (
                    <TabsContent
                        key={tab.value}
                        value={tab.value}
                    >
                        {tab.form({
                            ref,
                        })}
                    </TabsContent>
                ))}
            </View>
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
