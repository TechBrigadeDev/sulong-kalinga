import { useUser } from '~/features/user/user.hook';
import { TabList, Tabs, TabSlot, TabTrigger } from 'expo-router/ui';
import { View } from 'tamagui';
import { StyleSheet } from 'react-native';
import { useRouter } from 'expo-router';
import TabButton from '~/components/screens/Home/_components/button';


export default function Layout() {
  const router = useRouter();

  useUser();

  return (
    <Tabs>
      <View style={styles.tabContainer}>
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
            reset="always"
            asChild
          >
            <TabButton icon="EllipsisVertical">
              Options
            </TabButton>
          </TabTrigger>
          <TabTrigger
            name="/(tabs)/scheduling/care-worker/index"
            href="/(tabs)/scheduling/care-worker"
            style={{ display: 'none' }}
          />
          <TabTrigger
            name="/(tabs)/shifts/index"
            href="/(tabs)/shifts"
            style={{ display: 'none' }}
          />
          <TabTrigger
            name="/(tabs)/care-plan/index"
            href="/(tabs)/care-plan"
            style={{ display: 'none' }}
          />
        </View>
      </TabList>
    </Tabs>
  );
}

const styles = StyleSheet.create({
  tabContainer: {
    flex: 1,
  },
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
