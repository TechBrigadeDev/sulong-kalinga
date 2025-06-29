import React from "react";
import { StyleSheet } from "react-native";
import { Text } from "tamagui";

const Title = ({
    name: title,
}: {
    name: string;
}) => (
    <Text style={style.sectionTitle}>
        {title}
    </Text>
);

const style = StyleSheet.create({
    sectionTitle: {
        fontSize: 15,
        fontWeight: "bold",
        marginBottom: 10,
    },
});

export default Title;
