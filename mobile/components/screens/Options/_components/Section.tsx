import { StyleSheet } from "react-native";
import { YStack } from "tamagui";

const Section = ({
    children,
}: {
    children: React.ReactNode;
}) => {
    return (
        <YStack style={style.section}>
            {children}
        </YStack>
    );
};

const style = StyleSheet.create({
    section: {
        paddingBottom: 30,
    },
});

export default Section;
