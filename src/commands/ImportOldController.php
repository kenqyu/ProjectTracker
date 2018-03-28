<?php
namespace app\commands;

use app\models\enums\JobStatus;
use app\models\enums\UserRoles;
use app\models\enums\UserTypes;
use app\models\forms\RegisterForm;
use app\models\Job;
use app\models\JobComment;
use app\models\Justifications;
use app\models\OldJob;
use app\models\User;
use app\models\WorkType;
use GuzzleHttp\Client;
use League\Csv\Reader;
use Stringy\Stringy;
use yii\base\Exception;
use yii\base\InvalidParamException;
use yii\console\Controller;
use yii\db\Expression;

class ImportOldController extends Controller
{
    public function actionBuild()
    {
        $this->actionImportUsers();
        $this->actionUsers();
        $this->actionCreatedByUsers();
        $this->actionWorkTypes();
        $this->actionJustifications();
        $this->actionDo();
        $this->actionConvert();
    }

    public function actionDo()
    {
        $url = $this->prompt('Url to csv: ');
        if (empty($url)) {
            throw new InvalidParamException();
        }

        $c = new Client();
        $r = $c->get($url);

        $reader = Reader::createFromString($r->getBody());
        $first = true;
        foreach ($reader->fetchAssoc($reader->fetchOne()) as $index => $row) {
            if ($first) {
                $first = false;
                continue;
            }
            $model = new OldJob();
            $model->id = $row['ID'];
            $model->number = $row['JobNumber'];
            $model->name = $row['JobName'];
            $model->description = $row['Description'];
            $model->submitted_by = $row['SubmittedBy'];
            $model->submit_date = $row['SubmittalDate'];
            $model->rush = $row['Rush'] == 'Y' ? true : false;
            $model->due_date = $row['DueDate'];
            $model->work_type = $row['WorkType'];
            $model->justifications = $row['JustificationForRequest'];
            $model->dce_lead = $row['DCELead'];
            $model->status = $row['Status'];
            $model->last_date_user = $row['LastUpdatedBy'];
            $model->last_update_date = $row['LastUpdatedDate'];
            $model->comments = $row['Comments'];
            $model->it_notification = $row['ITNotification'];
            $model->iwcm_publishing_assignee = $row['IWCMPublishingAssignee'];
            $model->complete_date = $row['CompletedCancelledDate'];
            $model->current_url = $row['CurrentURL'];
            $model->ccc_impact = $row['CCCImpact'] == 'Y' ? true : false;
            $model->ccc_contact = $row['CCCContact'];
            $model->affiliate_compliance = $row['AffiliateCompliance'];
            $model->imcli = $row['IMCLI'] == 'Y' ? true : false;
            $model->related_olm = $row['RelatedOLM'];
            $model->sce_approvers = $row['SceApprovers'];
            $model->accounting = $row['Accounting'];
            $model->cwa = $row['CWA'];
            $model->estimate_amount = $row['EstimateAmount'];
            $model->translation_needed = $row['TranslationNeeded'] == 'Y' ? true : false;
            $model->translation_rush = $row['TranslationRush'] == 'Y' ? true : false;
            $model->translation_request_date = $row['TranslationRequestedDate'];
            $model->translation_due_date = $row['TranslationDueDate'];
            $model->translation_status = $row['TranslationStatus'];
            $model->attachment = $row['Attachment'];
            $model->invoice_number = $row['InvoiceNumber'];
            $model->invoice_amount = $row['InvoiceAmount'];
            $model->publishing_date = $row['PublishingDate'];
            $model->requestor_email = $row['RequestorEmailAddress'];
            $model->project_url = $row['ProjectURL'];
            $model->progress = $row['Progress'];
            $model->size = $row['Size'];

            if (!$model->save()) {
                var_dump($model->getErrors());
                die();
            }
        }
    }

    public function actionImportUsers()
    {
        $url = $this->prompt('Url to csv: ');
        if (empty($url)) {
            throw new InvalidParamException();
        }

        $c = new Client();
        $r = $c->get($url);

        $reader = Reader::createFromString($r->getBody());
        $first = true;
        foreach ($reader->fetchAssoc($reader->fetchOne()) as $index => $row) {
            if ($first) {
                $first = false;
                continue;
            }
            $model = new RegisterForm();
            $model->first_name = $row['FirstName'];
            $model->last_name = $row['LastName'];
            $model->email = $row['Email'];
            $model->password = \Yii::$app->security->generateRandomString(10);
            $model->password_repeat = $model->password;

            if (!$model->register(UserRoles::MANAGER)) {
                var_dump($model->getErrors());
                die();
            }
        }
    }

    public function actionConvert()
    {
        $models = collect(OldJob::find()->where(['NOT IN', 'status', ['Completed', 'Canceled']])->orWhere([
            '>=',
            'submit_date',
            '2016-06-01'
        ])->all());

        $statusMap = [
            '' => JobStatus::NEW,
            'On Hold' => JobStatus::ON_HOLD,
            'New' => JobStatus::NEW,
            'InProgress' => JobStatus::IN_PROGRESS,
            'Completed' => JobStatus::COMPLETED,
            'Canceled' => JobStatus::CANCELED,
            'Published' => JobStatus::COMPLETED,
            'Remediation' => JobStatus::IN_PROGRESS,
            'Creative' => JobStatus::IN_PROGRESS,
            'Consult' => JobStatus::IN_PROGRESS,
            'Translations' => JobStatus::IN_PROGRESS,
            'Translation' => JobStatus::IN_PROGRESS,
            'IT' => JobStatus::IN_PROGRESS,
        ];

        $user_map = collect([
            'Adriam, Adriana Anderson, adriana@tyscreative.com',
            'Adriana, Adriana Anderson, adriana@tyscreative.com',
            'Aja, Aja Clarke, Aja.Clarke@sce.com',
            'Angela Cortez, Angela Cortez, Angela.Cortez@sce.com',
            'Bao, Bao Unknown, notfound@scetracker.com',
            'Ben Vargas, Ben Vargas, Ben.Vargas@sce.com',
            'Bryan, Bryan Tan, bryan@tyscreative.com',
            'Carolyn, Carolyn Unknown, notfound@scetracker.com',
            'Carrie, Carrie Roberts, Carrie.Roberts@sce.com',
            'Carrie Roberts, Carrie Roberts, Carrie.Roberts@sce.com',
            'David, David Aguilar, david.a.aguilar@sce.com',
            'David A Aguilar, David Aguilar, david.a.aguilar@sce.com',
            'Eliud, Eliud Zamora, eliud@tyscreative.com',
            'Eluid, Eliud Zamora, eliud@tyscreative.com',
            'Eren, Eren Cello, notfound@scetracker.com',
            'Eren Cello, Eren Cello, notfound@scetracker.com',
            'Eren and David, Eren Cello, notfound@scetracker.com',
            'Gary, Gary Ramirez, gary@tyscreative.com',
            'Hoi Yip, Hoi Yip, Hoi.Yip@sce.com',
            'Ivan, Ivan Unknown, notfound@scetracker.com',
            'Kai, Kai Perng, kai@tyscreative.com',
            'Lawrence, Hoi Yip, Hoi.Yip@sce.com',
            'Lawrence CorpComm, Hoi Yip, Hoi.Yip@sce.com',
            'Mankin, Mankin Unknown, notfound@scetracker.com',
            'Michelle, Michelle Martinez, Michelle.A.Martinez@sce.com',
            'Misty, Misty Fong, misty@tyscreative.com',
            'Ram, Ram Koganti, Ram.Koganti@sce.com',
            'Sung, Sung Kim, Sung.Kim@sce.com',
            'Tin, Tin Yen, tin@tyscreative.com',
            //BATCH 2
            'Ken Perry, Ken Perry, notfound@scetracker.com',
            'Kendall Reichley, Kendall Reichley, kendall.reichley@sce.com ',
            'Larry Tabizon, Larry Tabizon, Larry.Tabizon@sce.com',
            'Le Quach, Le Quach, Le.Quach@sce.com',
            'Linda Malek, Linda Malek, Linda.Malek@sce.com',
            'Lorraine Espinosa Nall, Lorraine Nall, Lorraine.Espinosa.Nall@sce.com',
            'Louis Lopez, Louis Lopez, Louis.Lopez@sce.com',
            'Louise Songco, Louise Songco, Louise.Songco@sce.com',
            'MIchelle, MIchelle Martinez, Michelle.A.Martinez@sce.com',
            'Maria Gudino, Maria Gudino, Maria.Gudino@sce.com',
            'Martha Dobler, Martha Dobler, Martha.Dobler@sce.com',
            'Mary Hanway, Mary Hanway, Mary.Hanway@sce.com',
            'Mehboob Dhala, Mehboob Dhala, Mehboob.Dhala@sce.com',
            'Michael Padian, Michael Padian, Michael.Padian@sce.com',
            'Michelle, Michelle Martinez, Michelle.A.Martinez@sce.com',
            'Michelle A Martinez, Michelle Martinez, Michelle.A.Martinez@sce.com',
            'Mitch, Mitch Unknown, notfound@scetracker.com',
            'Myran Mahroo, Myran Mahroo, notfound@scetracker.com',
            'Nancy Gonzalez, Nancy Gonzalez, Nancy.Gonzalez@sce.com',
            'Nina Holmquist, Nina Holmquist, notfound@scetracker.com',
            'Pam Green, Pamela Greene, pamela.greene@sce.com',
            'Pam Greene, Pamela Greene, pamela.greene@sce.com',
            'Pam Phillips, Pamela Phillips, Pamela.Phillips@sce.com',
            'Pamela Phillips, Pamela Phillips, Pamela.Phillips@sce.com',
            'Pamela V Greene, Pamela Greene, pamela.greene@sce.com',
            'Patrice Brown, Patrice Brown, notfound@scetracker.com',
            'Paul Kasick, Paul Kasick, Paul.Kasick@sce.com',
            'Peggy Hsieh, Peggy Hsieh, notfound@scetracker.com',
            'Poloi Lin, Poloi Lin, Poloi.Lin@sce.com ',
            'Rachel Sherril, Rachel Sherril, notfound@scetracker.com',
            'Ram Koganti, Ram Koganti, Ram.Koganti@sce.com',
            'Rashon, Rashon Unknown, notfound@scetracker.com',
            'Ron Gales, Ron Gales, Ron.Gales@sce.com',
            'Rosie Aguirre, Rosie Aguirre, Rosie.Aguirre@sce.com',
            'Sarah Currid, Sarah Currid, notfound@scetracker.com',
            'Shirley Fortuna, Shirley Fortuna, Shirley.Fortuna@sce.com',
            'Starr Van Raalten, Starr Raalten, Starr.Vanraalten@sce.com',
            'Stephanie Young, Stephanie Young, Stephanie.A.Young@sce.com',
            'Ted Tayavibul, Ted Tayavibul, Ted.Tayavibul@sce.com',
            'Teru Williams, Teri Williams, Teri.Williams@sce.com',
            'Tod Bartholomay, Tod Bartholomay, Tod.Bartholomay@sce.com',
            'Vanessa Cabrera, Vanessa Cabrera, Vanessa.Cabrera@sce.com',
            'Vinnie Tucker, Vinnie Tucker, Vinvimarr.Tucker@sce.com',
            'Vinvimarr Tucker, Vinnie Tucker, Vinvimarr.Tucker@sce.com',
            'Dean,Dean Yoshitani, Vinvimarr.Tucker@sce.com',
            'Marisa George, Marisa George, Marisa.George@sce.com'
        ])->map(function ($item) {
            $tmp = explode(', ', $item);
            $tmp[1] = explode(' ', $tmp[1]);
            return $tmp;
        });

        $leads = [
            'Carrie' => 'Carrie.Roberts@sce.com',
            'Alex' => 'alex@tyscreative.com',
            'Adriana' => 'adriana@tyscreative.com',
            'David' => 'david.a.aguilar@sce.com',
            'Dean' => 'Dean.Yoshitani@sce.com',
            'Traci' => 'traci@tyscreative.com'
        ];

        $dce_lead = collect([
            'Aja' => 'Aja Clarke',
            'Alex' => 'Alex Chan',
            'Ariane' => 'Ariane Kirkland',
            'Ariane, Michelle' => 'Michelle Martinez',
            'Ariane, Vanessa' => 'Ariane Kirkland',
            'Cancel' => 'Adeline Ashley',
            'Carrie Roberts' => 'Carrie Roberts',
            'Carrie' => 'Carrie Roberts',
            'Chris, Myran' => 'Myran Mahroo',
            'DCE' => 'Adeline Ashley',
            'David' => 'David Aguilar',
            'Dean' => 'Dean Yoshitani',
            'Eliud' => 'Eliud Zamora',
            'Eren' => 'Eren Cello',
            'Marisa George, IW' => 'Marisa George',
            'Michelle' => 'Michelle Martinez',
            'Michelle, Adeline' => 'Michelle Martinez',
            'Michelle, Baret' => 'Michelle Martinez',
            'Michelle,  Adriana, TYS' => 'Adriana Anderson',
            'Myran' => 'Myran Mahroo',
            'Myran, Adriana' => 'Adriana Anderson',
            'Myran, Michelle' => 'Michelle Martinez',
            'Ravi, Anvesh, TYS' => 'Adriana Anderson',
            'Sung' => 'Sung Kim',
            'TYS' => 'Adriana Anderson',
            'Traci' => 'Traci Brown',
            'Vanessa' => 'Vanessa Cabrera',
            'Vanessa, Myran' => 'Vanessa Cabrera',
            'dce_lead' => 'Adeline Ashley'
        ])->map(function ($item) {
            return explode(' ', $item);
        });

        $submitedByMap = collect([
            '; Adriana Anderson; adriana@tyscreative.com',
            'Vinnie Tucker; Vinnie Tucker; Vinvimarr.Tucker@sce.com',
            'Adeline; Adeline Ashley; adeline.ashley@sce.com',
            'Adriana Anderson on behald of Adeline Ashley; Adriana Anderson; adriana@tyscreative.com',
            'Adriana Anderson on behalf; Adriana Anderson; adriana@tyscreative.com',
            'Adriana Anderson on behalf of Adeline Ashley; Adriana Anderson; adriana@tyscreative.com',
            'Adriana Anderson on behalf of Adeline Ashley (CX Request); Adriana Anderson; adriana@tyscreative.com',
            'Adriana Anderson on behalf of Adelline Ashley; Adriana Anderson; adriana@tyscreative.com',
            'Adriana Anderson on behalf of Aja Clarke; Adriana Anderson; adriana@tyscreative.com',
            'Adriana Anderson on behalf of Cari Young via CX; Adriana Anderson; adriana@tyscreative.com',
            'Adriana Anderson on behalf of Dany; Adriana Anderson; adriana@tyscreative.com',
            'Adriana Anderson on behalf of Frank Starke; Adriana Anderson; adriana@tyscreative.com',
            'Adriana Anderson on behalf of Larry; Adriana Anderson; adriana@tyscreative.com',
            'Adriana Anderson on behalf of Pam Phillips; Adriana Anderson; adriana@tyscreative.com',
            'Adriana Anderson on behalf of Patrick Riley; Adriana Anderson; adriana@tyscreative.com',
            'Adriana Anderson on behalf of Sung Kim; Adriana Anderson; adriana@tyscreative.com',
            'Adriana Andersonon behalf of Adeline Ashley; Adriana Anderson; adriana@tyscreative.com',
            'Ailsa Yew; Alisa Yew; Alisa.Yew@SCE.COM',
            'Air Dedman; Air Dedman; notfound@scetracker.com',
            'Al Shepetuk; Alexander Shepetuk; Alexander.Shepetuk@sce.com',
            'Albert Chang; Albert Chang; notfound@scetracker.com',
            'Albert Chang, Rashon; Albert Chang; notfound@scetracker.com',
            'Alexander Shepetuk; Alexander Shepetuk; Alexander.Shepetuk@sce.com',
            'Alexander Shepetuk, Arnaud Duteil; Alexander Shepetuk; Alexander.Shepetuk@sce.com',
            'Alexander Shepetuk, Arnaud DuteilAl; Alexander Shepetuk; Alexander.Shepetuk@sce.com',
            'Alice Seelig-Herndon; Alice Seelig-Herndon; Alice.Seelig.Herndon@sce.com',
            'Allison Worth; Allison Worth; Allison.Worth@sce.com',
            'Allyson Wehn; Allyson Wehn; notfound@scetracker.com',
            'Allyson When; Allyson Wehn; notfound@scetracker.com',
            'Amri; Amri Christianto; amri.christianto@sce.com',
            'Amri Christianto; Amri Christianto; amri.christianto@sce.com',
            'Ana Gonzalez; Ana Gonzalez; Ana.Gonzalez@sce.com',
            'Anabel S Ellison; Anabel Ellison; Anabel.S.Ellison@sce.com',
            'Anabelle Ellison; Anabel Ellison; Anabel.S.Ellison@sce.com',
            'Angela Reyes; Angela Reyes; Angela.Reyes@sce.com',
            'Angela Taglinao; Angela Taglinao; Angela.Taglinao@sce.com',
            'Anita Arnold; Anita Arnold; notfound@scetracker.com',
            'Anita Arnold, Pam Phillipls; Pamela Phillips; Pamela.Phillips@sce.com',
            'Anita Taylor; Anita Taylor; notfound@scetracker.com',
            'Anna Chung; Anna Chung; Anna.Chung@sce.com',
            'Ariane; Ariane Kirkland; Ariane.Kirkland@sce.com',
            'Ariane, Martha; Ariane Kirkland; Ariane.Kirkland@sce.com',
            'Ariane, Martha Dober; Ariane Kirkland; Ariane.Kirkland@sce.com',
            'Ariane, Martha Dobler; Martha Dobler; Martha.Dobler@sce.com',
            'Arnaud Duteil; Arnaud Duteil; Arnaud.Duteil@sce.com',
            'Arnaud Dutiel; Arnaud Duteil; Arnaud.Duteil@sce.com',
            'Ashley Sauer; Ashley Sauer; Ashley.Sauer@sce.com',
            'Austen D\'Lima; Austin DLima; Austin.Dlima@sce.com',
            'Austin Dlima; Austin DLima; Austin.Dlima@sce.com',
            'Baret; Baret Chakarian; Baret.Chakarian@sce.com',
            'Baret Chakarian; Baret Chakarian; Baret.Chakarian@sce.com',
            'Baret Chakarian, Hahn Seckner; Baret Chakarian; Baret.Chakarian@sce.com',
            'Benjamin Lee; Benjamin Lee; Benjamin.Lee@sce.com',
            'Benjamin Lee, Christopher Ohlheisa; Benjamin Lee; Benjamin.Lee@sce.com',
            'Beth Littlehales, Christopher Scott; Christopher Scott; Christopher.Scott@sce.com',
            'Binh Tang; Binh Tang; Binh.Tang@sce.com',
            'Brandi Anderson; Brandi Anderson; Brandi.Anderson@sce.com',
            'Brian Gatus; Brian Gatus; Brian.Gatus@sce.com',
            'Brian Kopec; Brian Kopec; Brian.Kopec@sce.com',
            'Brian Kopec, Mitch, Denise Parker; Brian Kopec; Brian.Kopec@sce.com',
            'Brian, Pam Greene; Pam Greene; pamela.greene@sce.com',
            'Cari Young; Cari Young; Cari.Young@sce.com',
            'Carissa Memovich; Carissa Memovich; Carissa.Memovich@sce.com',
            'Carissa Memovich via CX; Carissa Memovich; Carissa.Memovich@sce.com',
            'Carl Besaw; Carl Besaw; Carl.Besaw@sce.com',
            'Carrie Roberts; Carrie Roberts; Carrie.Roberts@sce.com',
            'Catherine Loan Vu Hoang; Catherine Hoang; Catherine.LoanVu.Hoang@sce.com',
            'CCC; Adeline Ashley; adeline.ashley@sce.com',
            'CCC, DCE; Adeline Ashley; adeline.ashley@sce.com',
            'Cedric Benton; Cedric Benton; Cedric.Benton@sce.com',
            'Charlene Stenger; Charlene Stenger; Charlene.Stenger@sce.com',
            'Chris Kjaer; Chris Kjaer; notfound@scetracker.com',
            'Chris Pahl; Chris Pahl; Chris.Pahl@sce.com',
            'Chris Tran; Chris Tran; Chris.Tran@sce.com',
            'Chris V; Christopher Vibert; Christopher.Vibert@sce.com',
            'Chris Vibert (CX Request); Christopher Vibert; Christopher.Vibert@sce.com',
            'Christian Que; Christian Que; Christian.Que@sce.com',
            'Christopher Que; Christian Que; Christian.Que@sce.com',
            'Christopher Scott; Christopher Scott; Christopher.Scott@sce.com',
            'Cindy Creed; Cindy Creed; cindy.creed@sce.com',
            'Consumer Affairs; Consumer Affairs; ConsumerAffairsAdministration@sce.com',
            'Corp Comm; Corporate Communications; CorporateCommunications@sce.com',
            'Corte Gaspar; Corte Gaspar; Corte.Gaspar@sce.com',
            'CPS Offer Management; CPS Offer Management; notfound@scetracker.com',
            'Cynthia King; Cynthia King; Cynthia.King@sce.com',
            'Dalilah Danaloz; Dalilah Danaloz; notfound@scetracker.com',
            'Danielle Manzano; Danielle Manzano; Danielle.Manzano@sce.com',
            'Dany; Dany Susanto; Dany.Susanto@sce.com',
            'Dany DCE; Dany Susanto; Dany.Susanto@sce.com',
            'Dario Moreno; Dario Moreno; notfound@scetracker.com',
            'David A Aguilar; David Aguilar; David.A.Aguilar@sce.com',
            'David Berndt, Joe Beck; Joe Beck; Joe.Beck@sce.com',
            'DCE; Adeline Ashley; adeline.ashley@sce.com',
            'DCE - Cust Sat; Adeline Ashley; adeline.ashley@sce.com',
            'DCE / Adeline; Adeline Ashley; adeline.ashley@sce.com',
            'DCE Cust Sat; Adeline Ashley; adeline.ashley@sce.com',
            'Dean Yoshitani; Dean Yoshitani; Dean.Yoshitani@sce.com',
            'Debbie Fellman; Debbie Fellman; notfound@scetracker.com',
            'Deborah Salinas; Deborah Salinas; Deborah.Salinas@sce.com',
            'Delia Jimenez; Delia Jimenez; Delia.M.Jimenez@sce.com',
            'Denise Parker; Denise Parker; Denise.Parker@sce.com',
            'Dennis Capili; Dennis Capili; Dennis.Capili@sce.com',
            'Diamond Andrews; Diamond Andrews; notfound@scetracker.com',
            'Dolores Bilbao; Dolores Bilbao; notfound@scetracker.com',
            'Eduyng Castano; Eduyng Castano; Eduyng.Castano@sce.com',
            'Elizabeth Naruko; Elizabeth Naruko; Elizabeth.Naruko@sce.com',
            'Eric Fisher, Patrice Brown; Eric Fisher; Eric.Fisher@sce.com',
            'Eric Murga; Eric Murga; Eric.Murga@sce.com',
            'Erica Gramajo; Erica Gramajo; Erica.Gramajo@sce.com',
            'Erica M; Erica Marquez; Erica.Marquez@sce.com',
            'Erica Montiel; Erica Montiel; notfound@scetracker.com',
            'Erica Montiel, Poloi Lin; Poloi Lin; Poloi.Lin@sce.com',
            'Estella Banuelos; Estella Banuelos; Estella.Banuelos@sce.com',
            'Eugene Ayuyao; Eugene Ayuyao; Eugene.Ayuyao@sce.com',
            'Fahime Dehkordi; Fahime Dehkordi; notfound@scetracker.com',
            'Frank Kao; Frank Kao; Frank.Kao@sce.com',
            'Frank Starke; Frank Starke; Frank.Starke@sce.com',
            'Frank Starke, Deborah Tillman; Frank Starke; Frank.Starke@sce.com',
            'Frank Yanez; Frank Yanes; Frank.Yanes@sce.com',
            'Gabby; Gabby Unkown; notfound@scetracker.com',
            'Gabriel Chavez; Gabriel Chavez; Gabriel.Chavez@sce.com',
            'Gary Suzuki; Gary Suzuki; Gary.Suzuki@sce.com',
            'Gary Suzuki, Pam Phillips; Gary Suzuki; Gary.Suzuki@sce.com',
            'Gloria Zapian Sakamoto; Gloria Zapian-Sakamoto; Gloria.Zapien@sce.com',
            'Graciela Leslie; Graciela Leslie; notfound@scetracker.com',
            'Graciella Leslie; Graciella Leslie; notfound@scetracker.com',
            'Grant Litman; Grant Littman; Grant.Littman@sce.com',
            'Grant, Wendy; Wendy Grant; notfound@scetracker.com',
            'Gwen via CX Request; Gwen Yamasaki; Gwen.Yamasaki@sce.com',
            'Gwen Yamasaki; Gwen Yamasaki; Gwen.Yamasaki@sce.com',
            'Gwen Yamasaki via CX; Gwen Yamasaki; Gwen.Yamasaki@sce.com',
            'Heather Torres; Heather Torres; heather.torres@sce.com',
            'Illary Archilla; Illary Archilla; notfound@scetracker.com',
            'Illary Archilla, Pam Phillips; Pamela Phillips; Pamela.Phillips@sce.com',
            'Jack Solis; Jack Solis; Jack.Solis@sce.com',
            'Jason Martin; Jason Martin; notfound@scetracker.com',
            'Jeff Lawrence; Jeff Lawrence; Jeff.Lawrence@sce.com',
            'Jeff Lidskin; Jeff Lidskin; Jeff.Lidskin@sce.com',
            'Jennifer Fernandez; Jennifer Fernandez; Jennifer.Fernandez@sce.com',
            'Jim Stevenson; James Stevenson; James.Stevenson@sce.com',
            'Joe Beck; Joe Beck; Joe.Beck@sce.com',
            'John Morton; John Morton; notfound@scetracker.com',
            'John Rankin; John Rankin; John.Rankin@sce.com',
            'Jonathan; Jonathan Unknown; notfound@scetracker.com',
            'Jonathan Kompara; Jonathan Kompara; Jonathan.Kompara@sce.com',
            'Jorge Rodriguez; Jorge Rodriguez; Jorge.Rodriguez@sce.com',
            'Jose Buendia; Jose Buendia; Jose.Buendia@sce.com',
            'Joseph Schmitt; Joseph Schmitt; Joseph.Schmitt@sce.com',
            'Joseph Smith; Joseph Schmitt; Joseph.Schmitt@sce.com',
            'Josheph Schmith; Joseph Schmitt; Joseph.Schmitt@sce.com',
            'Joycelyn Yue; Joycelyn Yue; Joycelyn.Yue@sce.com',
            'Kacie Clapp; Kacie Clapp; Kacie.Clapp@sce.com',
            'Kathie Conaway; Kathie Conaway; Kathie.Conaway@sce.com',
            'Kathryn Mickaliger; Kathryn Mickaliger; Kathryn.Mickaliger@sce.com',
            'Kelly Lieu; Kelly Lieu; Kelly.Lieu@sce.com',
            'Kendall Reichley; Kendall Reichley; kendall.reichley@sce.com',
            'Keneth Lau; Keneth Lau; notfound@scetracker.com',
            'Keneth Lau via CX; Keneth Lau; notfound@scetracker.com',
            'Kenneth Lau; Keneth Lau; notfound@scetracker.com',
            'Kevin Lam; Kevin Lam; notfound@scetracker.com',
            'Larry Tabazon; Larry Tabizon; Larry.Tabizon@sce.com',
            'Laura Ayala Huntley; Laura Ayala-Huntley; Laura.ayalahuntley@sce.com',
            'Laura Ayala-Huntley; Laura Ayala-Huntley; Laura.ayalahuntley@sce.com',
            'Laura Rosenbrook; Laura Rosenbrook; Laura.Rosenbrook@sce.com',
            'Lindsay Barlow; Lindsay Barlow; notfound@scetracker.com',
            'Lorraine Espinosa Nall; Lorraine Nall; Lorraine.Espinosa.Nall@sce.com',
            'Lorraine Espinosa-Nall; Lorraine Nall; Lorraine.Espinosa.Nall@sce.com',
            'Louis Lopez, Brian Kopec; Louis Lopez; Louis.Lopez@sce.com',
            'Luis Portillo; Luis Portillo; Luis.Portillo@sce.com',
            'Lynette Aquino; Lynette Aquino; notfound@scetracker.com',
            'Marina Trezos; Marina Trezos; Marina.Trezos@sce.com',
            'Marissa Barrera; Marissa Barrera; Marissa.Barrera@sce.com',
            'Marlene Montez; Marlene Montez; Marlene.Montez@sce.com',
            'Martha Dobler, Corte Gaspar; Martha Dobler; Martha.Dobler@sce.com',
            'Maureen Crabbe; Maureen Crabbe; Maureen.Crabbe@sce.com',
            'Melissa Woolley; Melissa Woolley; Melissa.Woolley@sce.com',
            'Michael Barigian; Michael Barigian; Michael.Barigian@sce.com',
            'Michael C Huyn; Michael Huynh; Michael.Huynh@sce.com',
            'Michael Guerra; Michael Guerra; Michael.Guerra@sce.com',
            'Michael Hsieh; Michael Hsieh; Michael.Hsieh@sce.com',
            'MIchelle; MIchelle Martinez; Michelle.A.Martinez@sce.com',
            'Michelle; MIchelle Martinez; Michelle.A.Martinez@sce.com',
            'Michelle A Martinez; MIchelle Martinez; Michelle.A.Martinez@sce.com',
            'Michelle Gascon; Michelle Gascon; notfound@scetracker.com',
            'Mike de la Hoz; Mike de la Hoz; Miguel.DelaHoz@sce.com',
            'Mindy McDonald; Mindy McDonald; Mindy.Mcdonald@sce.com',
            'Monica del Rosario; Monica Del Rosario; Monica.Delrosario@sce.com',
            'Myrtle Gaines; Myrtle Gaines; notfound@scetracker.com',
            'Namrita Merino; Namrita Merino; Namrita.Merino@sce.com',
            'Nancy Wilson; Nancy Wilson; notfound@scetracker.com',
            'Natalie Martinez; Natalie Martinez; Natalie.Martinez@sce.com',
            'Nicholas Roy; Nicholas Roy; Nicholas.Roy@sce.com',
            'Nina Duran; Nina Duran; Nina.Duran@sce.com',
            'Nina Perez; Nina Perez; notfound@scetracker.com',
            'Noel Bugarin; Noel Bugarin; Noel.Bugarin@sce.com',
            'Noel Hernandez, Marina Trezos; Marina Trezos; Marina.Trezos@sce.com',
            'Noel Hernandez, Susana Madrigal; Susana Madrigal; Susana.Madrigal@sce.com',
            'Norma Helms; Norma Helms; Norma.Helms@sce.com',
            'Pam Green; Pamela Greene; pamela.greene@sce.com',
            'Pam Greene; Pamela Greene; pamela.greene@sce.com',
            'Pam Greene, Gary Suzuki; Pamela Greene; pamela.greene@sce.com',
            'Pam Greene, Louis Lopez; Pamela Greene; pamela.greene@sce.com',
            'Pam Philips; Pamela Phillips; Pamela.Phillips@sce.com',
            'Pam Phillips; Pamela Phillips; Pamela.Phillips@sce.com',
            'Pam Phillips, John Rankin; Pamela Phillips; Pamela.Phillips@sce.com',
            'Pamela V Greene; Pamela Greene; pamela.greene@sce.com',
            'Pat Riley; Patrick Riley; Patrick.Riley@sce.com',
            'Patrick Riley; Patrick Riley; Patrick.Riley@sce.com',
            'Peggy Hsieh, Mike De La Hoz; Peggy Hsieh; notfound@scetracker.com',
            'PEV; PEV Unknown; notfound@scetracker.com',
            'Poloi Lin; Poloi Lin; Poloi.Lin@sce.com ',
            'Priscilla Ortez; Priscilla Ortiz; Priscilla.Ortiz@sce.com',
            'Priscilla Ortiz; Priscilla Ortiz; Priscilla.Ortiz@sce.com',
            'Priscilla Ortiz, Brian Kopec; Priscilla Ortiz; Priscilla.Ortiz@sce.com',
            'Rachel Sherril, Ken Perry; Rachel Sherril; notfound@scetracker.com',
            'Rashon; Rashon Unknown; notfound@scetracker.com',
            'Rashon, Jason Martin; Rashon Unknown; notfound@scetracker.com',
            'Ram Koganti; Ram Koganti; Ram.Koganti@sce.com',
            'Ricardo Piralta; Ricardo Piralta; notfound@scetracker.com',
            'Rita Sandoval; Rita Sandoval; Rita.Sandoval@sce.com',
            'Robert Hickerson; Robert Hickerson; notfound@scetracker.com',
            'Robyn Zander; Robyn Zander; Robyn.Zander@sce.com',
            'Rudy Rodriguez; Rudy Rodriguez; Ruby.Rodriguez@sce.com',
            'Sally VirgenRobert Hickerson; Sally Virgen; Sally.Virgen@sce.com',
            'San ea; San Ea; San.Ea@sce.com',
            'Sharrone McCall; Sharrone McCall; Sharrone.McCall@sce.com',
            'Sherry Bautista; Sherry Bautista; Sherry.Bautista@sce.com',
            'Shue M. Cheng; Shue Cheng; Shue.M.Cheng@sce.com',
            'Simi Luthra; Simi Luthra; notfound@scetracker.com',
            'Sony Mani; Sony Mani; Sony.Mani@sce.com',
            'Starr Van Raalten; Starr Raalten; Starr.Vanraalten@sce.com',
            'Steven Nguyen; Steven Nguyen; Steven.Nguyen@sce.com',
            'Susan Chang; Susan Chang; Susan.Chang@sce.com',
            'Susan Hart; Susan Chang; notfound@scetracker.com',
            'Ted Tayavibul and Anna Chung; Ted Tayavibul; Ted.Tayavibul@sce.com',
            'Teru Williams; Teri Williams; Teri.Williams@sce.com',
            'Theron Mehr; Theron Mehr; Theron.Mehr@sce.com',
            'Timothy Callaway; Timothy Callaway; Timothy.Callaway@sce.com',
            'Tom Walker; Tom Walker; Thomas.Walker@sce.com',
            'Unknown; Unknown Unknown; notfound@scetracker.com',
            'Vanessa; Vanessa Cabrera; Vanessa.Cabrera@sce.com',
            'Vanessa Cabrear; Vanessa Cabrera; Vanessa.Cabrera@sce.com',
            'Vanessa Canbrera; Vanessa Cabrera; Vanessa.Cabrera@sce.com',
            'Vanessa McGrady; Vanessa McGrady; notfound@scetracker.com',
            'Vanesssa Cabrera; Vanessa Cabrera; Vanessa.Cabrera@sce.com',
            'Venkata Reddy Kunam; Venkata Reddy Kunam; Venkata.Reddy.Kunam@sce.com',
            'Vikram Shivashankar; Vikram Shivashankar; Vikram.shivashankar@cognizant.com',
            'Vinvimarr Tucker; Vinnie Tucker; Vinvimarr.Tucker@sce.com',
            'Wendy Ethier; Wendy Ethier; Wendy.Ethier@sce.com',
            'Wendy Ethier, Kim Giglio; Wendy Ethier; Wendy.Ethier@sce.com',
            'Wendy Jao; Wendy Jao; notfound@scetracker.com',
        ])->map(function ($item) {
            $tmp = explode('; ', $item);
            $tmp[1] = explode(' ', $tmp[1]);
            return $tmp;
        });

        foreach ($models as $item) {
            /**
             * @var $item OldJob
             */
            $model = new Job();
            $model->old_id = $item->id;
            $model->name = $item->name . "";
            $model->legacy_id = $item->number;
            $model->description = $item->description;
            if (!empty($item->due_date)) {
                $model->due_date = $item->due_date;
            } else {
                $model->due_date = new Expression('NOW()');
            }
            $model->budget = 0;
            $model->status = $statusMap[$item->status];
            $model->approver = substr($item->sce_approvers, 0, 254);
            $model->cwa = $item->cwa;
            $model->completed_on = $item->complete_date;
            $model->ccc_impact = $item->ccc_impact;
            if (strpos($item->justifications, 'Mandated') !== false) {
                $model->mandate = true;
            }
            if (!empty($item->submit_date)) {
                $model->created_at = $item->submit_date;
            }
            if (!empty($item->last_update_date)) {
                $model->updated_at = $item->last_update_date;
            }

            $model->mandate = Stringy::create(implode(', ', $model->justifications))->contains('mandated', false);

            $user = $submitedByMap->where(0, $item->submitted_by);
            $tmp = null;
            if (!$user->isEmpty()) {
                $tmp = User::findOne(['first_name' => $user->first()[1][0], 'last_name' => $user->first()[1][1]]);
                if ($tmp) {
                    $model->creator_id = $tmp->id;
                } else {
                    throw new Exception('Cant find ' . $item->submitted_by . ' as creator');
                }
            } else {
                if (count(mb_split(' ', $item->submitted_by)) > 1) {
                    $tmp = User::findOne([
                        'first_name' => mb_split(' ', $item->submitted_by)[0],
                        'last_name' => mb_split(' ', $item->submitted_by)[1]
                    ]);
                    if ($tmp) {
                        $model->creator_id = $tmp->id;
                    } else {
                        throw new Exception('Cant find ' . $item->submitted_by . ' as creator');
                    }
                } else {
                    throw new Exception('Cant find ' . $item->submitted_by . ' as creator');
                }
            }

            if ($model->save()) {
                if (!empty($item->iwcm_publishing_assignee)) {
                    $user = $user_map->where(0, $item->iwcm_publishing_assignee);
                    if (!$user->isEmpty()) {
                        $tmp = User::findOne([
                            'first_name' => $user->first()[1][0],
                            'last_name' => $user->first()[1][1]
                        ]);
                        if ($tmp) {
                            $isTyped = false;
                            foreach ($tmp->userTypes as $ut) {
                                if ($ut->type_id == UserTypes::IWCM_PUBLISHING_ASSIGNEE) {
                                    $isTyped = true;
                                }
                            }
                            if (!$isTyped) {
                                $utm = new \app\models\UserTypes();
                                $utm->user_id = $tmp->id;
                                $utm->type_id = UserTypes::IWCM_PUBLISHING_ASSIGNEE;
                                $utm->save();
                            }
                            $model->iwcm_publishing_assignee_id = $tmp->id;
                        }
                    }
                }
                if (!empty($item->ccc_contact)) {
                    $user = $user_map->where(0, $item->ccc_contact);
                    if (!$user->isEmpty()) {
                        $tmp = User::findOne([
                            'first_name' => $user->first()[1][0],
                            'last_name' => $user->first()[1][1]
                        ]);
                        if ($tmp) {
                            $isTyped = false;
                            foreach ($tmp->userTypes as $ut) {
                                if ($ut->type_id == UserTypes::CCC_CONTACT) {
                                    $isTyped = true;
                                }
                            }
                            if (!$isTyped) {
                                $utm = new \app\models\UserTypes();
                                $utm->user_id = $tmp->id;
                                $utm->type_id = UserTypes::CCC_CONTACT;
                                $utm->save();
                            }
                            $model->ccc_contact_id = $tmp->id;
                        }
                    }
                }
                if (!empty($item->dce_lead)) {
                    if (isset($dce_lead[$item->dce_lead])) {
                        $tmp = User::findOne([
                            'first_name' => $dce_lead[$item->dce_lead][0],
                            'last_name' => $dce_lead[$item->dce_lead][1]
                        ]);
                        if ($tmp) {
                            $model->link('projectLead', $tmp);
                            $isTyped = false;
                            foreach ($tmp->userTypes as $ut) {
                                if ($ut->type_id == UserTypes::PROJECT_LEAD) {
                                    $isTyped = true;
                                }
                            }
                            if (!$isTyped) {
                                $utm = new \app\models\UserTypes();
                                $utm->user_id = $tmp->id;
                                $utm->type_id = UserTypes::PROJECT_LEAD;
                                $utm->save();
                            }
                        }
                    }
                }

                $comment = new JobComment();
                $comment->job_id = $model->id;
                $comment->body = $item->comments;
                $comment->user_id = $model->creator_id;
                $comment->save();

                $comment = new JobComment();
                $comment->job_id = $model->id;
                $comment->body = $item->accounting;
                $comment->user_id = $model->creator_id;
                $comment->save();

                if (!empty($item->justifications)) {
                    $tmp = explode(', ', $item->justifications);
                    foreach ($tmp as $j) {
                        $jm = Justifications::findOne(['name' => $j]);
                        if ($jm) {
                            $model->link('justifications', $jm);
                        }
                    }
                }
                if (!empty($item->work_type)) {
                    $tmp = explode(', ', $item->work_type);
                    foreach ($tmp as $wt) {
                        $wtm = WorkType::findOne(['name' => $wt]);
                        if ($wtm) {
                            $model->link('workTypes', $wtm);
                        }
                    }
                }
            } else {
                throw new Exception(json_encode($model->getErrors()) . ':' . json_encode($item->getAttributes()));
            }
        }
    }

    public function actionWorkTypes()
    {
        $new = [
            'Banner',
            'Carousel',
            'Friendly URL',
            'eSpots',
            'PDF Upload',
            'Copy Edit',
            'Copy Development',
            'Functionality',
            'Routine Request',
            'WCAG Remediation',
            'New Web Page',
            'Direct Mail',
            'Email',
            'Fact Sheet',
            'Brochures',
            'Images',
            'Research',
            'Data Pull',
            'Customer Journey',
            'Blue Printing',
            'Other'
        ];

        foreach ($new as $item) {
            $model = new WorkType();
            $model->name = $item;
            $model->save();
        }
    }

    public function actionJustifications()
    {
        $models = collect(OldJob::find()->where(['NOT IN', 'status', ['Completed', 'Canceled']])->orWhere([
            '>=',
            'submit_date',
            '2016-06-01'
        ])->all());

        $list = [];

        $models->unique('justifications')->each(function ($item) use (&$list) {
            $list = array_merge($list, explode(', ', $item->justifications));
        });


        foreach (collect($list)->unique()->filter(function ($item) {
            return !empty($item) && $item != "Mandated";
        }) as $item) {
            $model = new Justifications();
            $model->name = $item;
            $model->save();
        }
    }

    public function actionUsers()
    {
        $batch1 = collect([
            'Adriam, Adriana Anderson, adriana@tyscreative.com',
            'Adriana, Adriana Anderson, adriana@tyscreative.com',
            'Aja, Aja Clarke, Aja.Clarke@sce.com',
            'Angela Cortez, Angela Cortez, Angela.Cortez@sce.com',
            'Bao, Bao Unknown, notfound@scetracker.com',
            'Ben Vargas, Ben Vargas, Ben.Vargas@sce.com',
            'Bryan, Bryan Tan, bryan@tyscreative.com',
            'Carolyn, Carolyn Unknown, notfound@scetracker.com',
            'Carrie, Carrie Roberts, Carrie.Roberts@sce.com',
            'Carrie Roberts, Carrie Roberts, Carrie.Roberts@sce.com',
            'David, David Aguilar, david.a.aguilar@sce.com',
            'David A Aguilar, David Aguilar, david.a.aguilar@sce.com',
            'Eliud, Eliud Zamora, eliud@tyscreative.com',
            'Eluid, Eliud Zamora, eliud@tyscreative.com',
            'Eren, Eren Cello, notfound@scetracker.com',
            'Eren Cello, Eren Cello, notfound@scetracker.com',
            'Eren and David, Eren Cello, notfound@scetracker.com',
            'Gary, Gary Ramirez, gary@tyscreative.com',
            'Hoi Yip, Hoi Yip, Hoi.Yip@sce.com',
            'Ivan, Ivan Unknown, notfound@scetracker.com',
            'Kai, Kai Perng, kai@tyscreative.com',
            'Lawrence, Hoi Yip, Hoi.Yip@sce.com',
            'Lawrence CorpComm, Hoi Yip, Hoi.Yip@sce.com',
            'Mankin, Mankin Unknown, notfound@scetracker.com',
            'Michelle, Michelle Martinez, Michelle.A.Martinez@sce.com',
            'Misty, Misty Fong, misty@tyscreative.com',
            'Ram, Ram Koganti, Ram.Koganti@sce.com',
            'Sung, Sung Kim, Sung.Kim@sce.com',
            'Tin, Tin Yen, tin@tyscreative.com',
            //BATCH 2
            'Ken Perry, Ken Perry, notfound@scetracker.com',
            'Kendall Reichley, Kendall Reichley, kendall.reichley@sce.com ',
            'Larry Tabizon, Larry Tabizon, Larry.Tabizon@sce.com',
            'Le Quach, Le Quach, Le.Quach@sce.com',
            'Linda Malek, Linda Malek, Linda.Malek@sce.com',
            'Lorraine Espinosa Nall, Lorraine Nall, Lorraine.Espinosa.Nall@sce.com',
            'Louis Lopez, Louis Lopez, Louis.Lopez@sce.com',
            'Louise Songco, Louise Songco, Louise.Songco@sce.com',
            'MIchelle, MIchelle Martinez, Michelle.A.Martinez@sce.com',
            'Maria Gudino, Maria Gudino, Maria.Gudino@sce.com',
            'Martha Dobler, Martha Dobler, Martha.Dobler@sce.com',
            'Mary Hanway, Mary Hanway, Mary.Hanway@sce.com',
            'Mehboob Dhala, Mehboob Dhala, Mehboob.Dhala@sce.com',
            'Michael Padian, Michael Padian, Michael.Padian@sce.com',
            'Michelle, Michelle Martinez, Michelle.A.Martinez@sce.com',
            'Michelle A Martinez, Michelle Martinez, Michelle.A.Martinez@sce.com',
            'Mitch, Mitch Unknown, notfound@scetracker.com',
            'Myran Mahroo, Myran Mahroo, notfound@scetracker.com',
            'Nancy Gonzalez, Nancy Gonzalez, Nancy.Gonzalez@sce.com',
            'Nina Holmquist, Nina Holmquist, notfound@scetracker.com',
            'Pam Green, Pamela Greene, pamela.greene@sce.com',
            'Pam Greene, Pamela Greene, pamela.greene@sce.com',
            'Pam Phillips, Pamela Phillips, Pamela.Phillips@sce.com',
            'Pamela Phillips, Pamela Phillips, Pamela.Phillips@sce.com',
            'Pamela V Greene, Pamela Greene, pamela.greene@sce.com',
            'Patrice Brown, Patrice Brown, notfound@scetracker.com',
            'Paul Kasick, Paul Kasick, Paul.Kasick@sce.com',
            'Peggy Hsieh, Peggy Hsieh, notfound@scetracker.com',
            'Poloi Lin, Poloi Lin, Poloi.Lin@sce.com ',
            'Rachel Sherril, Rachel Sherril, notfound@scetracker.com',
            'Ram Koganti, Ram Koganti, Ram.Koganti@sce.com',
            'Rashon, Rashon Unknown, notfound@scetracker.com',
            'Ron Gales, Ron Gales, Ron.Gales@sce.com',
            'Rosie Aguirre, Rosie Aguirre, Rosie.Aguirre@sce.com',
            'Sarah Currid, Sarah Currid, notfound@scetracker.com',
            'Shirley Fortuna, Shirley Fortuna, Shirley.Fortuna@sce.com',
            'Starr Van Raalten, Starr Raalten, Starr.Vanraalten@sce.com',
            'Stephanie Young, Stephanie Young, Stephanie.A.Young@sce.com',
            'Ted Tayavibul, Ted Tayavibul, Ted.Tayavibul@sce.com',
            'Teru Williams, Teri Williams, Teri.Williams@sce.com',
            'Tod Bartholomay, Tod Bartholomay, Tod.Bartholomay@sce.com',
            'Vanessa Cabrera, Vanessa Cabrera, Vanessa.Cabrera@sce.com',
            'Vinnie Tucker, Vinnie Tucker, Vinvimarr.Tucker@sce.com',
            'Vinvimarr Tucker, Vinnie Tucker, Vinvimarr.Tucker@sce.com',
            'Dean, Dean Yoshitani, Vinvimarr.Tucker@sce.com',
            'Marisa George, Marisa George, Marisa.George@sce.com'
        ])->map(function ($item) {
            $tmp = explode(', ', $item);
            $tmp[1] = explode(' ', $tmp[1]);
            if ($tmp[2] == 'notfound@scetracker.com') {
                $tmp[2] = mb_strtolower(implode('.', $tmp[1])) . '_auto@scetracker.com';
            }
            return $tmp;
        })->unique(0)->each(function ($item) {
            $model = new RegisterForm;
            $model->first_name = $item[1][0];
            $model->last_name = $item[1][1];
            $model->email = $item[2];
            $model->password = \Yii::$app->security->generateRandomString(10);
            $model->password_repeat = $model->password;
            if (!$model->register(UserRoles::MANAGER)) {
                if (!$model->hasErrors('email')) {
                    var_dump($model->getErrors());
                }
            }
        });
    }

    public function actionCreatedByUsers()
    {
        $list = [
            'Vinnie Tucker; Vinnie Tucker; Vinvimarr.Tucker@sce.com',
            'Adeline; Adeline Ashley; adeline.ashley@sce.com',
            'Adriana Anderson on behald of Adeline Ashley; Adriana Anderson; adriana@tyscreative.com',
            'Adriana Anderson on behalf; Adriana Anderson; adriana@tyscreative.com',
            'Adriana Anderson on behalf of Adeline Ashley; Adriana Anderson; adriana@tyscreative.com',
            'Adriana Anderson on behalf of Adeline Ashley (CX Request); Adriana Anderson; adriana@tyscreative.com',
            'Adriana Anderson on behalf of Adelline Ashley; Adriana Anderson; adriana@tyscreative.com',
            'Adriana Anderson on behalf of Aja Clarke; Adriana Anderson; adriana@tyscreative.com',
            'Adriana Anderson on behalf of Cari Young via CX; Adriana Anderson; adriana@tyscreative.com',
            'Adriana Anderson on behalf of Dany; Adriana Anderson; adriana@tyscreative.com',
            'Adriana Anderson on behalf of Frank Starke; Adriana Anderson; adriana@tyscreative.com',
            'Adriana Anderson on behalf of Larry; Adriana Anderson; adriana@tyscreative.com',
            'Adriana Anderson on behalf of Pam Phillips; Adriana Anderson; adriana@tyscreative.com',
            'Adriana Anderson on behalf of Patrick Riley; Adriana Anderson; adriana@tyscreative.com',
            'Adriana Anderson on behalf of Sung Kim; Adriana Anderson; adriana@tyscreative.com',
            'Adriana Andersonon behalf of Adeline Ashley; Adriana Anderson; adriana@tyscreative.com',
            'Ailsa Yew; Alisa Yew; Alisa.Yew@SCE.COM',
            'Air Dedman; Air Dedman; notfound@scetracker.com',
            'Al Shepetuk; Alexander Shepetuk; Alexander.Shepetuk@sce.com',
            'Albert Chang; Albert Chang; notfound@scetracker.com',
            'Albert Chang, Rashon; Albert Chang; notfound@scetracker.com',
            'Alexander Shepetuk; Alexander Shepetuk; Alexander.Shepetuk@sce.com',
            'Alexander Shepetuk, Arnaud Duteil; Alexander Shepetuk; Alexander.Shepetuk@sce.com',
            'Alexander Shepetuk, Arnaud DuteilAl; Alexander Shepetuk; Alexander.Shepetuk@sce.com',
            'Alice Seelig-Herndon; Alice Seelig-Herndon; Alice.Seelig.Herndon@sce.com',
            'Allison Worth; Allison Worth; Allison.Worth@sce.com',
            'Allyson Wehn; Allyson Wehn; notfound@scetracker.com',
            'Allyson When; Allyson Wehn; notfound@scetracker.com',
            'Amri; Amri Christianto; amri.christianto@sce.com',
            'Amri Christianto; Amri Christianto; amri.christianto@sce.com',
            'Ana Gonzalez; Ana Gonzalez; Ana.Gonzalez@sce.com',
            'Anabel S Ellison; Anabel Ellison; Anabel.S.Ellison@sce.com',
            'Anabelle Ellison; Anabel Ellison; Anabel.S.Ellison@sce.com',
            'Angela Reyes; Angela Reyes; Angela.Reyes@sce.com',
            'Angela Taglinao; Angela Taglinao; Angela.Taglinao@sce.com',
            'Anita Arnold; Anita Arnold; notfound@scetracker.com',
            'Anita Arnold, Pam Phillipls; Pamela Phillips; Pamela.Phillips@sce.com',
            'Anita Taylor; Anita Taylor; notfound@scetracker.com',
            'Anna Chung; Anna Chung; Anna.Chung@sce.com',
            'Ariane; Ariane Kirkland; Ariane.Kirkland@sce.com',
            'Ariane, Martha; Ariane Kirkland; Ariane.Kirkland@sce.com',
            'Ariane, Martha Dober; Ariane Kirkland; Ariane.Kirkland@sce.com',
            'Ariane, Martha Dobler; Martha Dobler; Martha.Dobler@sce.com',
            'Arnaud Duteil; Arnaud Duteil; Arnaud.Duteil@sce.com',
            'Arnaud Dutiel; Arnaud Duteil; Arnaud.Duteil@sce.com',
            'Ashley Sauer; Ashley Sauer; Ashley.Sauer@sce.com',
            'Austen D\'Lima; Austin DLima; Austin.Dlima@sce.com',
            'Austin Dlima; Austin DLima; Austin.Dlima@sce.com',
            'Baret; Baret Chakarian; Baret.Chakarian@sce.com',
            'Baret Chakarian; Baret Chakarian; Baret.Chakarian@sce.com',
            'Baret Chakarian, Hahn Seckner; Baret Chakarian; Baret.Chakarian@sce.com',
            'Benjamin Lee; Benjamin Lee; Benjamin.Lee@sce.com',
            'Benjamin Lee, Christopher Ohlheisa; Benjamin Lee; Benjamin.Lee@sce.com',
            'Beth Littlehales, Christopher Scott; Christopher Scott; Christopher.Scott@sce.com',
            'Binh Tang; Binh Tang; Binh.Tang@sce.com',
            'Brandi Anderson; Brandi Anderson; Brandi.Anderson@sce.com',
            'Brian Gatus; Brian Gatus; Brian.Gatus@sce.com',
            'Brian Kopec; Brian Kopec; Brian.Kopec@sce.com',
            'Brian Kopec, Mitch, Denise Parker; Brian Kopec; Brian.Kopec@sce.com',
            'Brian, Pam Greene; Pam Greene; pamela.greene@sce.com',
            'Cari Young; Cari Young; Cari.Young@sce.com',
            'Carissa Memovich; Carissa Memovich; Carissa.Memovich@sce.com',
            'Carissa Memovich via CX; Carissa Memovich; Carissa.Memovich@sce.com',
            'Carl Besaw; Carl Besaw; Carl.Besaw@sce.com',
            'Carrie Roberts; Carrie Roberts; Carrie.Roberts@sce.com',
            'Catherine Loan Vu Hoang; Catherine Hoang; Catherine.LoanVu.Hoang@sce.com',
            'CCC; Adeline Ashley; adeline.ashley@sce.com',
            'CCC, DCE; Adeline Ashley; adeline.ashley@sce.com',
            'Cedric Benton; Cedric Benton; Cedric.Benton@sce.com',
            'Charlene Stenger; Charlene Stenger; Charlene.Stenger@sce.com',
            'Chris Kjaer; Chris Kjaer; notfound@scetracker.com',
            'Chris Pahl; Chris Pahl; Chris.Pahl@sce.com',
            'Chris Tran; Chris Tran; Chris.Tran@sce.com',
            'Chris V; Christopher Vibert; Christopher.Vibert@sce.com',
            'Chris Vibert (CX Request); Christopher Vibert; Christopher.Vibert@sce.com',
            'Christian Que; Christian Que; Christian.Que@sce.com',
            'Christopher Que; Christian Que; Christian.Que@sce.com',
            'Christopher Scott; Christopher Scott; Christopher.Scott@sce.com',
            'Cindy Creed; Cindy Creed; cindy.creed@sce.com',
            'Consumer Affairs; Consumer Affairs; ConsumerAffairsAdministration@sce.com',
            'Corp Comm; Corporate Communications; CorporateCommunications@sce.com',
            'Corte Gaspar; Corte Gaspar; Corte.Gaspar@sce.com',
            'CPS Offer Management; CPS Offer Management; notfound@scetracker.com',
            'Cynthia King; Cynthia King; Cynthia.King@sce.com',
            'Dalilah Danaloz; Dalilah Danaloz; notfound@scetracker.com',
            'Danielle Manzano; Danielle Manzano; Danielle.Manzano@sce.com',
            'Dany; Dany Susanto; Dany.Susanto@sce.com',
            'Dany DCE; Dany Susanto; Dany.Susanto@sce.com',
            'Dario Moreno; Dario Moreno; notfound@scetracker.com',
            'David A Aguilar; David Aguilar; David.A.Aguilar@sce.com',
            'David Berndt, Joe Beck; Joe Beck; Joe.Beck@sce.com',
            'DCE; Adeline Ashley; adeline.ashley@sce.com',
            'DCE - Cust Sat; Adeline Ashley; adeline.ashley@sce.com',
            'DCE / Adeline; Adeline Ashley; adeline.ashley@sce.com',
            'DCE Cust Sat; Adeline Ashley; adeline.ashley@sce.com',
            'Dean Yoshitani; Dean Yoshitani; Dean.Yoshitani@sce.com',
            'Debbie Fellman; Debbie Fellman; notfound@scetracker.com',
            'Deborah Salinas; Deborah Salinas; Deborah.Salinas@sce.com',
            'Delia Jimenez; Delia Jimenez; Delia.M.Jimenez@sce.com',
            'Denise Parker; Denise Parker; Denise.Parker@sce.com',
            'Dennis Capili; Dennis Capili; Dennis.Capili@sce.com',
            'Diamond Andrews; Diamond Andrews; notfound@scetracker.com',
            'Dolores Bilbao; Dolores Bilbao; notfound@scetracker.com',
            'Eduyng Castano; Eduyng Castano; Eduyng.Castano@sce.com',
            'Elizabeth Naruko; Elizabeth Naruko; Elizabeth.Naruko@sce.com',
            'Eric Fisher, Patrice Brown; Eric Fisher; Eric.Fisher@sce.com',
            'Eric Murga; Eric Murga; Eric.Murga@sce.com',
            'Erica Gramajo; Erica Gramajo; Erica.Gramajo@sce.com',
            'Erica M; Erica Marquez; Erica.Marquez@sce.com',
            'Erica Montiel; Erica Montiel; notfound@scetracker.com',
            'Erica Montiel, Poloi Lin; Poloi Lin; Poloi.Lin@sce.com',
            'Estella Banuelos; Estella Banuelos; Estella.Banuelos@sce.com',
            'Eugene Ayuyao; Eugene Ayuyao; Eugene.Ayuyao@sce.com',
            'Fahime Dehkordi; Fahime Dehkordi; notfound@scetracker.com',
            'Frank Kao; Frank Kao; Frank.Kao@sce.com',
            'Frank Starke; Frank Starke; Frank.Starke@sce.com',
            'Frank Starke, Deborah Tillman; Frank Starke; Frank.Starke@sce.com',
            'Frank Yanez; Frank Yanes; Frank.Yanes@sce.com',
            'Gabby; Gabby Unkown; notfound@scetracker.com',
            'Gabriel Chavez; Gabriel Chavez; Gabriel.Chavez@sce.com',
            'Gary Suzuki; Gary Suzuki; Gary.Suzuki@sce.com',
            'Gary Suzuki, Pam Phillips; Gary Suzuki; Gary.Suzuki@sce.com',
            'Gloria Zapian Sakamoto; Gloria Zapian-Sakamoto; Gloria.Zapien@sce.com',
            'Graciela Leslie; Graciela Leslie; notfound@scetracker.com',
            'Graciella Leslie; Graciella Leslie; notfound@scetracker.com',
            'Grant Litman; Grant Littman; Grant.Littman@sce.com',
            'Grant, Wendy; Wendy Grant; notfound@scetracker.com',
            'Gwen via CX Request; Gwen Yamasaki; Gwen.Yamasaki@sce.com',
            'Gwen Yamasaki; Gwen Yamasaki; Gwen.Yamasaki@sce.com',
            'Gwen Yamasaki via CX; Gwen Yamasaki; Gwen.Yamasaki@sce.com',
            'Heather Torres; Heather Torres; heather.torres@sce.com',
            'Illary Archilla; Illary Archilla; notfound@scetracker.com',
            'Illary Archilla, Pam Phillips; Pamela Phillips; Pamela.Phillips@sce.com',
            'Jack Solis; Jack Solis; Jack.Solis@sce.com',
            'Jason Martin; Jason Martin; notfound@scetracker.com',
            'Jeff Lawrence; Jeff Lawrence; Jeff.Lawrence@sce.com',
            'Jeff Lidskin; Jeff Lidskin; Jeff.Lidskin@sce.com',
            'Jennifer Fernandez; Jennifer Fernandez; Jennifer.Fernandez@sce.com',
            'Jim Stevenson; James Stevenson; James.Stevenson@sce.com',
            'Joe Beck; Joe Beck; Joe.Beck@sce.com',
            'John Morton; John Morton; notfound@scetracker.com',
            'John Rankin; John Rankin; John.Rankin@sce.com',
            'Jonathan; Jonathan Unknown; notfound@scetracker.com',
            'Jonathan Kompara; Jonathan Kompara; Jonathan.Kompara@sce.com',
            'Jorge Rodriguez; Jorge Rodriguez; Jorge.Rodriguez@sce.com',
            'Jose Buendia; Jose Buendia; Jose.Buendia@sce.com',
            'Joseph Schmitt; Joseph Schmitt; Joseph.Schmitt@sce.com',
            'Joseph Smith; Joseph Schmitt; Joseph.Schmitt@sce.com',
            'Josheph Schmith; Joseph Schmitt; Joseph.Schmitt@sce.com',
            'Joycelyn Yue; Joycelyn Yue; Joycelyn.Yue@sce.com',
            'Kacie Clapp; Kacie Clapp; Kacie.Clapp@sce.com',
            'Kathie Conaway; Kathie Conaway; Kathie.Conaway@sce.com',
            'Kathryn Mickaliger; Kathryn Mickaliger; Kathryn.Mickaliger@sce.com',
            'Kelly Lieu; Kelly Lieu; Kelly.Lieu@sce.com',
            'Kendall Reichley; Kendall Reichley; kendall.reichley@sce.com ',
            'Keneth Lau; Keneth Lau; notfound@scetracker.com',
            'Keneth Lau via CX; Keneth Lau; notfound@scetracker.com',
            'Kenneth Lau; Keneth Lau; notfound@scetracker.com',
            'Kevin Lam; Kevin Lam; notfound@scetracker.com',
            'Larry Tabazon; Larry Tabizon; Larry.Tabizon@sce.com',
            'Laura Ayala Huntley; Laura Ayala-Huntley; Laura.ayalahuntley@sce.com',
            'Laura Ayala-Huntley; Laura Ayala-Huntley; Laura.ayalahuntley@sce.com',
            'Laura Rosenbrook; Laura Rosenbrook; Laura.Rosenbrook@sce.com',
            'Lindsay Barlow; Lindsay Barlow; notfound@scetracker.com',
            'Lorraine Espinosa Nall; Lorraine Nall; Lorraine.Espinosa.Nall@sce.com',
            'Lorraine Espinosa-Nall; Lorraine Nall; Lorraine.Espinosa.Nall@sce.com',
            'Louis Lopez, Brian Kopec; Louis Lopez; Louis.Lopez@sce.com',
            'Luis Portillo; Luis Portillo; Luis.Portillo@sce.com',
            'Lynette Aquino; Lynette Aquino; notfound@scetracker.com',
            'Marina Trezos; Marina Trezos; Marina.Trezos@sce.com',
            'Marissa Barrera; Marissa Barrera; Marissa.Barrera@sce.com',
            'Marlene Montez; Marlene Montez; Marlene.Montez@sce.com',
            'Martha Dobler, Corte Gaspar; Martha Dobler; Martha.Dobler@sce.com',
            'Maureen Crabbe; Maureen Crabbe; Maureen.Crabbe@sce.com',
            'Melissa Woolley; Melissa Woolley; Melissa.Woolley@sce.com',
            'Michael Barigian; Michael Barigian; Michael.Barigian@sce.com',
            'Michael C Huyn; Michael Huynh; Michael.Huynh@sce.com',
            'Michael Guerra; Michael Guerra; Michael.Guerra@sce.com',
            'Michael Hsieh; Michael Hsieh; Michael.Hsieh@sce.com',
            'Michelle; MIchelle Martinez; Michelle.A.Martinez@sce.com',
            'Michelle A Martinez; MIchelle Martinez; Michelle.A.Martinez@sce.com',
            'Michelle Gascon; Michelle Gascon; notfound@scetracker.com',
            'Mike de la Hoz; Mike de la Hoz; Miguel.DelaHoz@sce.com',
            'Mindy McDonald; Mindy McDonald; Mindy.Mcdonald@sce.com',
            'Monica del Rosario; Monica Del Rosario; Monica.Delrosario@sce.com',
            'Myrtle Gaines; Myrtle Gaines; notfound@scetracker.com',
            'Namrita Merino; Namrita Merino; Namrita.Merino@sce.com',
            'Nancy Wilson; Nancy Wilson; notfound@scetracker.com',
            'Natalie Martinez; Natalie Martinez; Natalie.Martinez@sce.com',
            'Nicholas Roy; Nicholas Roy; Nicholas.Roy@sce.com',
            'Nina Duran; Nina Duran; Nina.Duran@sce.com',
            'Nina Perez; Nina Perez; notfound@scetracker.com',
            'Noel Bugarin; Noel Bugarin; Noel.Bugarin@sce.com',
            'Noel Hernandez, Marina Trezos; Marina Trezos; Marina.Trezos@sce.com',
            'Noel Hernandez, Susana Madrigal; Susana Madrigal; Susana.Madrigal@sce.com',
            'Norma Helms; Norma Helms; Norma.Helms@sce.com',
            'Pam Green; Pamela Greene; pamela.greene@sce.com',
            'Pam Greene; Pamela Greene; pamela.greene@sce.com',
            'Pam Greene, Gary Suzuki; Pamela Greene; pamela.greene@sce.com',
            'Pam Greene, Louis Lopez; Pamela Greene; pamela.greene@sce.com',
            'Pam Philips; Pamela Phillips; Pamela.Phillips@sce.com',
            'Pam Phillips; Pamela Phillips; Pamela.Phillips@sce.com',
            'Pam Phillips, John Rankin; Pamela Phillips; Pamela.Phillips@sce.com',
            'Pamela V Greene; Pamela Greene; pamela.greene@sce.com',
            'Pat Riley; Patrick Riley; Patrick.Riley@sce.com',
            'Patrick Riley; Patrick Riley; Patrick.Riley@sce.com',
            'Peggy Hsieh, Mike De La Hoz; Peggy Hsieh; notfound@scetracker.com',
            'PEV; PEV Unknown; notfound@scetracker.com',
            'Poloi Lin; Poloi Lin; Poloi.Lin@sce.com ',
            'Priscilla Ortez; Priscilla Ortiz; Priscilla.Ortiz@sce.com',
            'Priscilla Ortiz; Priscilla Ortiz; Priscilla.Ortiz@sce.com',
            'Priscilla Ortiz, Brian Kopec; Priscilla Ortiz; Priscilla.Ortiz@sce.com',
            'Rachel Sherril, Ken Perry; Rachel Sherril; notfound@scetracker.com',
            'Rashon; Rashon Unknown; notfound@scetracker.com',
            'Rashon, Jason Martin; Rashon Unknown; notfound@scetracker.com',
            'Ricardo Piralta; Ricardo Piralta; notfound@scetracker.com',
            'Rita Sandoval; Rita Sandoval; Rita.Sandoval@sce.com',
            'Robert Hickerson; Robert Hickerson; notfound@scetracker.com',
            'Robyn Zander; Robyn Zander; Robyn.Zander@sce.com',
            'Rudy Rodriguez; Rudy Rodriguez; Ruby.Rodriguez@sce.com',
            'Sally VirgenRobert Hickerson; Sally Virgen; Sally.Virgen@sce.com',
            'San ea; San Ea; San.Ea@sce.com',
            'Sharrone McCall; Sharrone McCall; Sharrone.McCall@sce.com',
            'Sherry Bautista; Sherry Bautista; Sherry.Bautista@sce.com',
            'Shue M. Cheng; Shue Cheng; Shue.M.Cheng@sce.com',
            'Simi Luthra; Simi Luthra; notfound@scetracker.com',
            'Sony Mani; Sony Mani; Sony.Mani@sce.com',
            'Starr Van Raalten; Star Rallten; Starr.Vanraalten@sce.com',
            'Steven Nguyen; Steven Nguyen; Steven.Nguyen@sce.com',
            'Susan Chang; Susan Chang; Susan.Chang@sce.com',
            'Susan Hart; Susan Chang; notfound@scetracker.com',
            'Ted Tayavibul and Anna Chung; Ted Tayavibul; Ted.Tayavibul@sce.com',
            'Teru Williams; Teri Williams; Teri.Williams@sce.com',
            'Theron Mehr; Theron Mehr; Theron.Mehr@sce.com',
            'Timothy Callaway; Timothy Callaway; Timothy.Callaway@sce.com',
            'Tom Walker; Tom Walker; Thomas.Walker@sce.com',
            'Unknown; Unknown Unknown; notfound@scetracker.com',
            'Vanessa; Vanessa Cabrera; Vanessa.Cabrera@sce.com',
            'Vanessa Cabrear; Vanessa Cabrera; Vanessa.Cabrera@sce.com',
            'Vanessa Canbrera; Vanessa Cabrera; Vanessa.Cabrera@sce.com',
            'Vanessa McGrady; Vanessa McGrady; notfound@scetracker.com',
            'Vanesssa Cabrera; Vanessa Cabrera; Vanessa.Cabrera@sce.com',
            'Venkata Reddy Kunam; Venkata Reddy Kunam; Venkata.Reddy.Kunam@sce.com',
            'Vikram Shivashankar; Vikram Shivashankar; Vikram.shivashankar@cognizant.com',
            'Vinvimarr Tucker; Vinnie Tucker; Vinvimarr.Tucker@sce.com',
            'Wendy Ethier; Wendy Ethier; Wendy.Ethier@sce.com',
            'Wendy Ethier, Kim Giglio; Wendy Ethier; Wendy.Ethier@sce.com',
            'Wendy Jao; Wendy Jao; notfound@scetracker.com',
            'Anthony Saucedo; Anthony Saucedo; notfound@scetracker.com',
            'Nam Le; Nam Le; Nam.Le@sce.com ',
            'Lori Atwater; Lori Atwater; Lori.Atwater@sce.com',
            'Ofelia Rodriguez; Ofelia Rodriguez; Ofelia.Rodriguez@sce.com',
            'Kristina Falkner; Kristina Falkner; Kristina.Falkner@sce.com',
        ];
        collect($list)->map(function ($item) {
            $tmp = explode('; ', $item);
            $tmp[1] = explode(' ', $tmp[1]);
            if ($tmp[2] == 'notfound@scetracker.com') {
                $tmp[2] = mb_strtolower(implode('.', $tmp[1])) . '_auto@scetracker.com';
            }
            return $tmp;
        })->each(function ($item) {
            $model = new RegisterForm;
            $model->first_name = $item[1][0];
            $model->last_name = $item[1][1];
            $model->email = trim($item[2]);
            $model->password = \Yii::$app->security->generateRandomString(10);
            $model->password_repeat = $model->password;
            if (!$model->register()) {
                if (!$model->hasErrors('email')) {
                    var_dump($model->getErrors());
                }
            }
        });
    }
}
