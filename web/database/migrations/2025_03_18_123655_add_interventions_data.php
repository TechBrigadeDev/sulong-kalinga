<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('interventions')->insert([
            ['care_category_id' => 1, 'intervention_description' => 'Assist/aid in sitting'],
            ['care_category_id' => 1, 'intervention_description' => 'Support/aid in walking and other movements'],
            ['care_category_id' => 1, 'intervention_description' => 'Transfer/move from bed to wheelchair'],
            ['care_category_id' => 1, 'intervention_description' => 'Aide in using assistive device'],
            ['care_category_id' => 1, 'intervention_description' => 'Assist in using the toilet'],
            ['care_category_id' => 1, 'intervention_description' => 'Assistance is getting to the health center, hospital, and other health facilities.'],
            ['care_category_id' => 1, 'intervention_description' => 'Assist in repositioning in bed'],

            ['care_category_id' => 2, 'intervention_description' => 'Communicate by using clear and concise language, avoiding complex sentences or jargon.'],
            ['care_category_id' => 2, 'intervention_description' => 'Use pictures, symbols, or gestures to support communication.'],
            ['care_category_id' => 2, 'intervention_description' => 'Repeat important information to reinforce understanding.'],
            ['care_category_id' => 2, 'intervention_description' => 'Provide prompts or cues to help the individual recall information or complete tasks.'],
            ['care_category_id' => 2, 'intervention_description' => 'Explore devices such as tablets or smartphones with communication apps.'],
            ['care_category_id' => 2, 'intervention_description' => 'Facilitate playing of simple memory games to exercise cognitive function: with card games like Solitaire, Rummy, or Bridge and board games like chess, checkers, or Scrabble.'],
            ['care_category_id' => 2, 'intervention_description' => 'Work on puzzles (jigsaw or word) to improve problem-solving skills.'],
            ['care_category_id' => 2, 'intervention_description' => 'Encourage reading materials that are age-appropriate and interesting (story telling)'],
            ['care_category_id' => 2, 'intervention_description' => 'Use music to stimulate memories and evoke emotions.'],
            ['care_category_id' => 2, 'intervention_description' => 'Explore creative activities to express thoughts and feelings (Art therapy) such as drawing and painting'],
            ['care_category_id' => 2, 'intervention_description' => 'Encourage to participate in singing groups or sing along to favorite songs.'],
            ['care_category_id' => 2, 'intervention_description' => 'Writing: Keeping a journal, writing short stories, or poetry.'],
            ['care_category_id' => 2, 'intervention_description' => 'Scrapbooking: Collecting and organizing memories in a scrapbook.'],
            ['care_category_id' => 2, 'intervention_description' => 'Knitting and Crocheting: Creating items with yarn.'],
            ['care_category_id' => 2, 'intervention_description' => 'Label items in the home with clear and simple labels.'],
            ['care_category_id' => 2, 'intervention_description' => 'Maintain a consistent daily routine to help the individual feel more oriented.'],
            ['care_category_id' => 2, 'intervention_description' => 'Minimize clutter to reduce confusion and overwhelm.'],
            ['care_category_id' => 2, 'intervention_description' => 'Ensure adequate natural light to help with orientation and mood.'],

            ['care_category_id' => 3, 'intervention_description' => 'Hygiene Care: Hand washing'],
            ['care_category_id' => 3, 'intervention_description' => 'Hygiene Care: Combing'],
            ['care_category_id' => 3, 'intervention_description' => 'Hygiene Care: Tooth brushing'],
            ['care_category_id' => 3, 'intervention_description' => 'Hygiene Care: Nail clipping'],
            ['care_category_id' => 3, 'intervention_description' => 'Hygiene Care: Changing clothes'],
            ['care_category_id' => 3, 'intervention_description' => 'Hygiene Care: Perineal care'],
            ['care_category_id' => 3, 'intervention_description' => 'Bathing'],
            ['care_category_id' => 3, 'intervention_description' => 'Diaper Changing'],
            ['care_category_id' => 3, 'intervention_description' => 'Feeding'],

            ['care_category_id' => 4, 'intervention_description' => 'Ensure that the individual is taking medications as prescribed and understanding their purpose.'],
            ['care_category_id' => 4, 'intervention_description' => 'Use medication reminders, pill organizers, or caregiver assistance to help with medication adherence.'],
            ['care_category_id' => 4, 'intervention_description' => 'Store medications safely and out of reach to prevent accidental overdose or misuse.'],
            ['care_category_id' => 4, 'intervention_description' => 'Back care (light massage)'],
            ['care_category_id' => 4, 'intervention_description' => 'Breathing Exercise'],
            ['care_category_id' => 4, 'intervention_description' => 'Light stretching/ exercise'],

            ['care_category_id' => 5, 'intervention_description' => 'Assist in attending Senior Citizens activities'],
            ['care_category_id' => 5, 'intervention_description' => 'Assist in going to the Senior Citizens Day Center'],
            ['care_category_id' => 5, 'intervention_description' => 'Encourage visits from family, friends, or caregivers to provide companionship and emotional support.'],
            ['care_category_id' => 5, 'intervention_description' => 'Arrange regular phone calls or other communication mediums with loved ones to maintain social connections.'],
            ['care_category_id' => 5, 'intervention_description' => 'Connect with support groups for individuals with chronic diseases'],

            ['care_category_id' => 6, 'intervention_description' => 'Going outside for fresh air and sunlight'],
            ['care_category_id' => 6, 'intervention_description' => 'Walking in parks and other areas in the community'],
            ['care_category_id' => 6, 'intervention_description' => 'Encourage/Assist in spending time outdoors'],
            ['care_category_id' => 6, 'intervention_description' => 'Gardening activities'],

            ['care_category_id' => 7, 'intervention_description' => 'Regular Cleaning Daily Tasks: Talk with family members/ Facilitate cleaning surfaces, dusting, and mopping with the family members.'],
            ['care_category_id' => 7, 'intervention_description' => 'Regular Cleaning Weekly Tasks: Assist/ Facilitate family members in changing bed linens, and cleaning bathrooms, and laundry.'],
            ['care_category_id' => 7, 'intervention_description' => 'Cooking: Preparing meals that are easy to eat and nutritious.'],
            ['care_category_id' => 7, 'intervention_description' => 'Dishwashing: Assist family members/ Facilitate washing dishes and cleaning up after meals.'],
            ['care_category_id' => 7, 'intervention_description' => 'Laundry: Washing and drying laundry (towels and clothes)'],
            ['care_category_id' => 7, 'intervention_description' => 'Laundry: Folding and ironing clothes if needed'],
            ['care_category_id' => 7, 'intervention_description' => 'Laundry: Organizing clothes in closets or drawers']
        ]);

        DB::table('interventions_tagalog')->insert([
            ['care_category_id' => 1, 't_intervention_description' => 'Tumulong/tulong sa pag-upo'],
            ['care_category_id' => 1, 't_intervention_description' => 'Suporta/tulong sa paglalakad at iba pang galaw'],
            ['care_category_id' => 1, 't_intervention_description' => 'Paglipat mula sa kama patungo sa wheelchair'],
            ['care_category_id' => 1, 't_intervention_description' => 'Tulong sa paggamit ng assistive device'],
            ['care_category_id' => 1, 't_intervention_description' => 'Tumulong sa paggamit ng palikuran'],
            ['care_category_id' => 1, 't_intervention_description' => 'Pag-assist sa pagpunta sa mga health center, ospital at iba pang pasilidad ng kalusugan'],
            ['care_category_id' => 1, 't_intervention_description' => 'Tumulong sa muling pagpoposisyon sa kama'],

            ['care_category_id' => 2, 't_intervention_description' => 'Makipag-usap sa pamamagitan ng paggamit ng malinaw at maigsi na wika, pag-iwas sa mga kumplikadong pangungusap o jargon.'],
            ['care_category_id' => 2, 't_intervention_description' => 'Gumamit ng mga larawan, simbolo o kilos para suportahan ang komunikasyon'],
            ['care_category_id' => 2, 't_intervention_description' => 'Ulitin ang mahahalagang impormasyon upang mapalakas ang pang-unawa'],
            ['care_category_id' => 2, 't_intervention_description' => 'Magbigay ng mga prompt o pahiwatig upang matulungan ang indibidwal na maalala ang impormasyon o kumpletuhin ang mga gawain.'],
            ['care_category_id' => 2, 't_intervention_description' => 'I-explore ang mga device gaya ng mga tablet o smartphone na may mga app sa komunikasyon'],
            ['care_category_id' => 2, 't_intervention_description' => 'Pag-facilitate ng mga simpleng laro ng memorya upang magamit ang pag-andar ng pag-iisip tulad ng: mga larong card tulad ng Solitaire, Rummy o Bridge at mga board games tulad ng chess, checkers o scrabble'],
            ['care_category_id' => 2, 't_intervention_description' => 'Gumawa ng mga puzzles (jigsaw or word) upang mapabuti ang mga kasanayan sa paglutas ng problema.'],
            ['care_category_id' => 2, 't_intervention_description' => 'Paghikayat sa mga materyales ng pagbabasa na naaangkop sa edad at kawili-wili.'],
            ['care_category_id' => 2, 't_intervention_description' => 'Gumamit ng musika upang pasiglahin ang mga alaala at pukawin ang mga emosyon'],
            ['care_category_id' => 2, 't_intervention_description' => 'Pag-explore ng mga malikhaing aktibidad upang ipahayag ang mga saloobin at damdamin (Art Therapy) tulad ng pagguhit at pagpipinta.'],
            ['care_category_id' => 2, 't_intervention_description' => 'Himukin na lumahok sa mga singing groups o sumabay sa mga paboritong kanta.'],
            ['care_category_id' => 2, 't_intervention_description' => 'Pagpapasulat ng maikling kwento, o tula.'],
            ['care_category_id' => 2, 't_intervention_description' => 'Scrapbooking: Pagkolekta at pag-aayos ng mga alaala sa isang scrapbook.'],
            ['care_category_id' => 2, 't_intervention_description' => 'Knitting and Crocheting: Paggawa ng mga bagay na may sinulid.'],
            ['care_category_id' => 2, 't_intervention_description' => 'Lagyan ng label ang mga bagay sa bahay ng malinaw at simpleng mga label.'],
            ['care_category_id' => 2, 't_intervention_description' => 'Panatilihin ang isang pare-parehong pang-araw araw na gawain upang matulungan ang indibidwal na maging mas nakatuon.'],
            ['care_category_id' => 2, 't_intervention_description' => 'I-minimize ang kalat upang mabawasan ang pagkalito at mapuspos.'],
            ['care_category_id' => 2, 't_intervention_description' => 'Tiyakin ang sapat na natural na liwanag upang makatulong sa oryentasyon at mood.'],

            ['care_category_id' => 3, 't_intervention_description' => 'Pangangalaga sa Kalinisan (Hygiene Care): Paghuhugas ng kamay/Hand washing'],
            ['care_category_id' => 3, 't_intervention_description' => 'Pangangalaga sa Kalinisan (Hygiene Care): Pagsusuklay/ Combing'],
            ['care_category_id' => 3, 't_intervention_description' => 'Pangangalaga sa Kalinisan (Hygiene Care): Pagsisipilyo ng ngipin/ Tooth brushing'],
            ['care_category_id' => 3, 't_intervention_description' => 'Pangangalaga sa Kalinisan (Hygiene Care): Pagbaklo sa kulo/ Nail clipping'],
            ['care_category_id' => 3, 't_intervention_description' => 'Pangangalaga sa Kalinisan (Hygiene Care): Pagpapali ng damit/ Changing clothes'],
            ['care_category_id' => 3, 't_intervention_description' => 'Pangangalaga sa Kalinisan (Hygiene Care): Perineal care'],
            ['care_category_id' => 3, 't_intervention_description' => 'Pagpapaligo/ Bathing'],
            ['care_category_id' => 3, 't_intervention_description' => 'Pagpapalit ng diaper/ Diaper changing'],
            ['care_category_id' => 3, 't_intervention_description' => 'Pagpapakain/ Feeding'],

            ['care_category_id' => 4, 't_intervention_description' => 'Tiyakin na ang indibidwal ay umiinom ng mga gamot ayon sa inireseta at nauunawaan ang layunin.'],
            ['care_category_id' => 4, 't_intervention_description' => 'Gumamit ng mga paalala sa gamot, pill organizer, o tulong ng tagapag-alaga upang tumulong sa pagsunod sa gamot.'],
            ['care_category_id' => 4, 't_intervention_description' => 'Iimbak ang mga gamot ng ligtas at hindi maaabot para maiwasan ang aksidenteng overdose o maling paggamit.'],
            ['care_category_id' => 4, 't_intervention_description' => 'Pangangalaga sa likod/Back care (light massage)'],
            ['care_category_id' => 4, 't_intervention_description' => 'Pag-eehersisyo sa paghinga/Breathing Exercise'],
            ['care_category_id' => 4, 't_intervention_description' => 'Banayad na stretching/Light stretching/ exercise'],

            ['care_category_id' => 5, 't_intervention_description' => 'Tumulong sa pagdalo sa mga aktibidad ng Senior Citizens'],
            ['care_category_id' => 5, 't_intervention_description' => 'Tumulong sa pagpunta sa Senior Citizen Day Center'],
            ['care_category_id' => 5, 't_intervention_description' => 'Hikayatin ang mga pagbisita mula sa pamilya, kaibigan, o tagapag-alaga upang magbigay ng companionship at emosyonal na suporta.'],
            ['care_category_id' => 5, 't_intervention_description' => 'Ayusin ang mga regular na tawag sa telepono o iba pang mga medium ng komunikasyon sa mga mahal sa buhay upang mapanatili ang mga social na koneksyon.'],
            ['care_category_id' => 5, 't_intervention_description' => 'Kumonekta sa mga grupo ng suporta para sa mga indibidwal na may malalang sakit.'],

            ['care_category_id' => 6, 't_intervention_description' => 'Paglabas para sa sariwang hangin at sikat ng araw.'],
            ['care_category_id' => 6, 't_intervention_description' => 'Paglalakad sa mga parke at iba pang lugar sa komunidad'],
            ['care_category_id' => 6, 't_intervention_description' => 'Hikayatin/Tulungan ang paggugol ng oras sa labas'],
            ['care_category_id' => 6, 't_intervention_description' => 'Mga aktibidad sa paghahalaman'],

            ['care_category_id' => 7, 't_intervention_description' => 'Regular Cleaning Pang-araw-araw na Gawain: Makipag-usap sa mga miyembro ng pamilya/ Padaliin ang paglilinis ng mga ibabaw, pag-aalis ng alikabok, at pagmo-mop sa mga miyembro ng pamilya.'],
            ['care_category_id' => 7, 't_intervention_description' => 'Regular Cleaning Lingguhang Gawain: Tulungan/Pangasiwaan ang mga miyembro ng pamilya sa pagpapalit ng mga bed linen, at paglilinis ng mga banyo, at paglalaba.'],
            ['care_category_id' => 7, 't_intervention_description' => 'Pagluluto: Paghahanda ng mga pagkain na madaling kainin at masustansya.'],
            ['care_category_id' => 7, 't_intervention_description' => 'Paghuhugas ng pinggan: Tulungan ang mga miyembro ng pamilya/ Padaliin ang paghuhugas ng pinggan at paglilinis pagkatapos kumain.'],
            ['care_category_id' => 7, 't_intervention_description' => 'Laundry: Paglalaba at Pagpapatuyo ng labahan (towel & sinul-ot la nga bado)'],
            ['care_category_id' => 7, 't_intervention_description' => 'Laundry: Pagtupi at pamamalantsa ng mga damit kung kinakailangan'],
            ['care_category_id' => 7, 't_intervention_description' => 'Laundry: Pag-aayos ng mga damit sa mga aparador o drawer'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('interventions')->truncate();
        DB::table('interventions_tagalog')->truncate();
    }
};
