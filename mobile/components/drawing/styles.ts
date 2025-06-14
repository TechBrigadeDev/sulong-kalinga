import { StyleSheet } from "react-native";

export const drawingStyles = StyleSheet.create({
    labelContainer: {
        position: "absolute",
        top: 0,
        left: 0,
        right: 0,
        bottom: 0,
        justifyContent: "center",
        alignItems: "center",
        pointerEvents: "none",
    },
    signHereLabel: {
        fontSize: 24,
        color: "#888",
        fontStyle: "italic",
        textAlign: "center",
    },
    canvasStyle: {
        position: "absolute",
        top: 0,
        left: 0,
        right: 0,
        bottom: 80, // Leave space for button bar
    },
    buttonBar: {
        position: "absolute",
        bottom: 0,
        left: 0,
        right: 0,
        height: 80,
        flexDirection: "row",
        justifyContent: "space-around",
        alignItems: "center",
        backgroundColor:
            "rgba(255, 255, 255, 0.95)",
        borderTopWidth: 1,
        borderTopColor: "#E0E0E0",
        paddingHorizontal: 20,
    },
    orientationOverlay: {
        position: "absolute",
        top: 0,
        left: 0,
        right: 0,
        bottom: 0,
        backgroundColor: "rgba(0, 0, 0, 0.9)",
        justifyContent: "center",
        alignItems: "center",
        zIndex: 1000,
    },
    orientationContent: {
        alignItems: "center",
        paddingHorizontal: 40,
    },
    phoneIcon: {
        alignItems: "center",
        marginBottom: 20,
    },
    phoneIconText: {
        fontSize: 64,
        marginBottom: 10,
    },
    rotateArrow: {
        fontSize: 32,
        color: "#4A90E2",
        fontWeight: "bold",
    },
    orientationTitle: {
        fontSize: 24,
        fontWeight: "bold",
        color: "white",
        textAlign: "center",
        marginBottom: 12,
    },
    orientationSubtitle: {
        fontSize: 16,
        color: "#CCCCCC",
        textAlign: "center",
        lineHeight: 24,
    },
});
