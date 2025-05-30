import { StyleSheet } from "react-native";
import { Card, CardProps } from "tamagui";

interface Props extends CardProps {
    children?: React.ReactNode;
}

const OptionCard = ({
    children,
    style
}: Props) => {
    return (
        <Card style={[styles.card, style]}>
            {children}
        </Card>
    )
}

const styles = StyleSheet.create({
    card: {
        backgroundColor: "#fff",
    }
});

export default OptionCard;