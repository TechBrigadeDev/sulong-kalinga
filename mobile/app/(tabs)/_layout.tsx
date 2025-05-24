import { useUser } from '~/features/user/user.hook';
import { TabList, Tabs, TabSlot, TabTrigger } from 'expo-router/ui';
import Lucide from "@react-native-vector-icons/lucide";
import { View } from 'tamagui';
import { StyleSheet } from 'react-native';
import TabButton from '../../components/screens/Home/components/button';
import { useRouter } from 'expo-router';


export default function Layout() {
  const router = useRouter();

  useUser();

  return (
    <Tabs>
      <TabSlot/>
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
            name="(tabs)/options"
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
    elevation: 5,
    marginBottom: 20,
    marginHorizontal: 10,
    borderRadius: 50 
  },
})
