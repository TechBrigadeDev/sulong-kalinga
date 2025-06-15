import EmergencyAssistanceForm from "features/emergency-service/emergency/_components/form";
import ServiceForm from "features/emergency-service/service/form";
import { ReactNode } from "react";
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

const EmergencyServiceFormSelector = () => {
    return (
        <Tabs
            defaultValue={tabs[0].value}
            orientation="horizontal"
            flexDirection="column"
            // bg="yellow"
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
