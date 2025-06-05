import MaskedView from "@react-native-masked-view/masked-view";
import { FlashList, FlashListProps } from "@shopify/flash-list";
import { LinearGradient } from "expo-linear-gradient";
import { StyleSheet, View } from "react-native";

type FlatListProps<T> = Omit<FlashListProps<T>, 'data' | 'renderItem'> & {
  data: T[];
  renderItem: ({ item }: { item: T }) => React.ReactElement;
  estimatedItemSize?: number;
};

function FlatList<T>({
  data,
  renderItem,
  estimatedItemSize = 100,
  contentContainerStyle,
  ...props
}: FlatListProps<T>) {
  return (
    <MaskedView
      style={{ flex: 1 }}
      maskElement={
        <View style={{ flex: 1 }}>
          <LinearGradient
            colors={['transparent', '#000000', '#000000', 'transparent']}
            locations={[0, 0.1, 0.9, 1]}
            style={StyleSheet.absoluteFill}
            start={{ x: 0, y: 0.0 }}
            end={{ x: 0, y: 0.85 }}
          />
        </View>
      }
    >
      <FlashList
        {...props}
        data={data}
        renderItem={renderItem}
        estimatedItemSize={estimatedItemSize}
        showsVerticalScrollIndicator={false}
        contentContainerStyle={{
          paddingVertical: 24,
          paddingBottom: 120,
          ...contentContainerStyle as any
        }}
      />
    </MaskedView>
  );
}

export default FlatList;