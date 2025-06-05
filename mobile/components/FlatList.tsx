import { FlashList, FlashListProps } from "@shopify/flash-list";

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
    <FlashList
      {...props}
      data={data}
      renderItem={renderItem}
      estimatedItemSize={estimatedItemSize}
      contentContainerStyle={{
        paddingBottom: 120,
        ...contentContainerStyle as any
      }}
    />
  );
}

export default FlatList;