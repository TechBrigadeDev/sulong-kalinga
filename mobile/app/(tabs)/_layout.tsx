import { useUser } from '~/features/user/user.hook';
import { TabList, Tabs, TabSlot, TabTrigger } from 'expo-router/ui';
import { View } from 'tamagui';
import { SafeAreaView, StyleSheet } from 'react-native';
import { useRouter } from 'expo-router';
import TabButton from '~/components/screens/Home/_components/button';


export default function Layout() {
  const router = useRouter();

  useUser();

  return (
    <Tabs>
      <View style={{ flex: 1 }}>
        <TabSlot/>
      </View>
      <TabList asChild>
        <View
         style={styles.tabList}
        >
          <TabButton
            icon="MessageCircle"
            onPressIn={() => router.push('/messaging')}
          >
            Messaging
          </TabButton>
          <TabTrigger
            name="(tabs)/index"
            href="/(tabs)"
            asChild
          >
           <TabButton icon="House">
              Home
            </TabButton>
          </TabTrigger>
          <TabTrigger
            name="/(tabs)/options/index"
            href="/(tabs)/options"
            asChild
          >
            <TabButton icon="EllipsisVertical">
              Options
            </TabButton>
          </TabTrigger>
        </View>
      </TabList>
    </Tabs>
  );
}

const styles = StyleSheet.create({
  tabList: {
    flexDirection: 'row',
    justifyContent: 'space-around',
    paddingVertical: 10,
    backgroundColor: '#ffffff',
    marginBottom: 20,
    position: 'absolute',
    bottom: 10,
    left: 20,
    right: 20,

    borderRadius: 50,
    shadowColor: '#000',
    shadowOffset: {
      width: 0,
      height: 5,
    },
    shadowOpacity: 0.2,
    shadowRadius: 5,
  },
})
