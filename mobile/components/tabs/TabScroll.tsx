import { ScrollView } from "tamagui";

const TabScroll = ({
    children,
    contentContainerStyle,
    ...props
}: Props) => {
    return (
        <ScrollView
            contentContainerStyle={{
                paddingBlockEnd: 100
            }}
            {...props}
        >
            {children}
        </ScrollView>
    )
}

type Props = ScrollView['props'];

export default TabScroll;