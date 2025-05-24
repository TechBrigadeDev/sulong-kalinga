import { StatusBar, Dimensions, Platform } from "react-native";

const getTopBarHeight = () => {
  const statusBarHeight = StatusBar.currentHeight || 0;
  const topBarHeight =
    Platform.OS === "ios" ? statusBarHeight + 44 : statusBarHeight + 56;

  return topBarHeight;
};

export const topBarHeight = getTopBarHeight();