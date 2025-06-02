import TabScroll from "components/tabs/TabScroll";
import React from 'react';
import { SafeAreaView, StyleSheet, View } from 'react-native';
import { Text,YStack } from 'tamagui';

import { ICareManager } from "~/features/user/management/management.type";

import CareManagerHeader from './components/CareManagerHeader';
import ContactInformation from './components/ContactInformation';
import Documents from './components/Documents';
import GovernmentIDs from './components/GovernmentIDs';
import PersonalDetails from './components/PersonalDetails';

interface CareManagerDetailProps {
    careManager: ICareManager;
}

function CareManagerDetail({ careManager }: CareManagerDetailProps) {
    if (!careManager) {
        return (
            <View style={styles.centered}>
                <Text>Care Manager data is not available</Text>
            </View>
        );
    }

    return (
        <SafeAreaView style={styles.container}>
            <TabScroll>
                <YStack gap="$4" style={{ padding: 16 }}>
                    <CareManagerHeader careManager={careManager} />
                    <PersonalDetails careManager={careManager} />
                    <ContactInformation careManager={careManager} />
                    <Documents careManager={careManager} />
                    <GovernmentIDs careManager={careManager} />
                </YStack>
            </TabScroll>
        </SafeAreaView>
    );
}

const styles = StyleSheet.create({
    container: {
        flex: 1,
    },
    centered: {
        flex: 1,
        justifyContent: 'center',
        alignItems: 'center',
        padding: 16
    }
});

export default CareManagerDetail;