<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_76 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 76");
		$check = $this->db->query("SHOW COLUMNS FROM `permissions` LIKE 'description'");
		if(!$check->num_rows()){
		$this->db->query("ALTER TABLE `permissions` ADD `description` VARCHAR(255) NOT NULL");
		}
		$this->db->query("update permissions set description = 'The user can create and edit appointment attendee slots and availability' where permission_name = 'slot config'");
$this->db->query("update permissions set description = 'The user can view the data counts report' where permission_name = 'data counts'");
$this->db->query("update permissions set description = 'The user can manage the shop/cart/order configuration' where permission_name = 'admin shop'");
$this->db->query("update permissions set description = 'The user can move a record to another campaign' where permission_name = 'change campaign'");
$this->db->query("update permissions set description = 'The user can change a records icon' where permission_name = 'change icon'");
$this->db->query("update permissions set description = 'The user can change the source of the record' where permission_name = 'change source'");
$this->db->query("update permissions set description = 'The user can move the record to another data pot' where permission_name = 'change pot'");
$this->db->query("update permissions set description = 'The user can change the color of a record' where permission_name = 'change color'");
$this->db->query("update permissions set description = 'The user can park, unpark and remove records on the system' where permission_name = 'record options'");
$this->db->query("update permissions set description = 'The user can find a full address by using just a postcode. This feature uses a 3rd party API and may cost credits' where permission_name = 'get address'");
$this->db->query("update permissions set description = 'The user can edit the history of a record' where permission_name = 'edit outcome'");
$this->db->query("update permissions set description = 'The user can add multiple entries to the additional info panel' where permission_name = 'add custom items'");
$this->db->query("update permissions set description = 'The user will be displayed in reports. Managers probably don\'t need this options' where permission_name = 'report on'");
$this->db->query("update permissions set description = 'The user can export appointments to an ICS file used to import to other calendars such as google/outlook' where permission_name = 'export ics'");
$this->db->query("update permissions set description = 'The user can import a google/outlook ICS file into the system' where permission_name = 'import ics'");
$this->db->query("update permissions set description = 'Records are automatically added to the attendees planner when an appointment is made for them' where permission_name = 'apps to planner'");
$this->db->query("update permissions set description = 'The user must set a call direction (inbound/outbound) when they update a record' where permission_name = 'set call direction'");
$this->db->query("update permissions set description = 'The user can view and amend the planner of other users' where permission_name = 'admin planner'");
$this->db->query("update permissions set description = 'The user will automatically take ownership of a record when they update it' where permission_name = 'take ownership'");
$this->db->query("update permissions set description = 'The user only has access to complete a survey' where permission_name = 'survey only'");
$this->db->query("update permissions set description = 'The user only has access to the files and folders feature. Can be used for client accounts that need to send and retrieve files' where permission_name = 'files only'");
$this->db->query("update permissions set description = 'The user has access to the SMS report' where permission_name = 'sms'");
$this->db->query("update permissions set description = 'The user has access to various system related options. This is required to access any pages within this submenu' where permission_name = 'system menu'");
$this->db->query("update permissions set description = 'The user is able to send SMS messages from with a record' where permission_name = 'send sms'");
$this->db->query("update permissions set description = 'The user will be shown a timer when they dial a telephone number' where permission_name = 'use timer'");
$this->db->query("update permissions set description = 'The user can add/edit other users on the system' where permission_name = 'admin users'");
$this->db->query("update permissions set description = 'The user can manage permissions and roles on the system' where permission_name = 'admin roles'");
$this->db->query("update permissions set description = 'The user can add/edit teams on the system' where permission_name = 'admin teams'");
$this->db->query("update permissions set description = 'The user can add/edit custom fields linked to a campaign' where permission_name = 'campaign fields'");
$this->db->query("update permissions set description = 'The user can add/edit groups on the system' where permission_name = 'admin groups'");
$this->db->query("update permissions set description = 'The user has access to the planner' where permission_name = 'planner'");
$this->db->query("update permissions set description = 'The user can add/edit campaigns on the system' where permission_name = 'campaign setup'");
$this->db->query("update permissions set description = 'The user can control which users have access to a campaign' where permission_name = 'campaign access'");
$this->db->query("update permissions set description = 'The user has access to various database features. IE. Migrate/Reset/Backup' where permission_name = 'database'");
$this->db->query("update permissions set description = 'The user has access to the productivity report' where permission_name = 'productivity'");
$this->db->query("update permissions set description = 'The user can add/edit park codes within the system' where permission_name = 'parkcodes'");
$this->db->query("update permissions set description = 'The user can manage record suppression within the system' where permission_name = 'suppression'");
$this->db->query("update permissions set description = 'The user can search and managae duplicate records' where permission_name = 'duplicates'");
$this->db->query("update permissions set description = 'The user can add/edit outcome triggers for a campaign' where permission_name = 'triggers'");
$this->db->query("update permissions set description = 'The user can add/edit outcomes on the system' where permission_name = 'edit outcomes'");
$this->db->query("update permissions set description = 'The user can add/edit CSV exports' where permission_name = 'edit export'");
$this->db->query("update permissions set description = 'The user can access the actions menu from the search page. Used to bulk park and bulk email based on search criteria.' where permission_name = 'search actions'");
$this->db->query("update permissions set description = 'The user can view the dashboard' where permission_name = 'view dashboard'");
$this->db->query("update permissions set description = 'The user can search for files they have access to' where permission_name = 'search files'");
$this->db->query("update permissions set description = 'The user can add new files to folders they have access to' where permission_name = 'add files'");
$this->db->query("update permissions set description = 'The user can delete files from folders they have access to' where permission_name = 'delete files'");
$this->db->query("update permissions set description = 'The user can view the list records page' where permission_name = 'list records'");
$this->db->query("update permissions set description = 'The user can manage file and folder permissions on the system' where permission_name = 'admin files'");
$this->db->query("update permissions set description = 'The user can view and download files from folders they have access to' where permission_name = 'view files'");
$this->db->query("update permissions set description = 'The user has access to the files submenu' where permission_name = 'files menu'");
$this->db->query("update permissions set description = 'The user will automatically be assigned to a record they view if nobody else already owns it' where permission_name = 'keep records'");
$this->db->query("update permissions set description = 'Records with tasks pending will be included in the list view by default' where permission_name = 'view pending'");
$this->db->query("update permissions set description = 'Live records will be included in the list view by default' where permission_name = 'view live'");
$this->db->query("update permissions set description = 'Completed records will be included in the list view by default' where permission_name = 'view completed'");
$this->db->query("update permissions set description = 'Dead records will be included in the list view by default' where permission_name = 'view dead'");
$this->db->query("update permissions set description = 'Parked records will be included in the list view by default' where permission_name = 'view parked'");
$this->db->query("update permissions set description = 'Unassigned records will be included in the list view by default' where permission_name = 'view unassigned'");
$this->db->query("update permissions set description = 'The user has access to the \"Start dialling\" feature' where permission_name = 'use callpot'");
$this->db->query("update permissions set description = 'The user can manage the daily data rations' where permission_name = 'ration data'");
$this->db->query("update permissions set description = 'The user has access to the data archiving features' where permission_name = 'archive data'");
$this->db->query("update permissions set description = 'The user has access to the data export page' where permission_name = 'export data'");
$this->db->query("update permissions set description = 'The user can import records using a CSV file from the import menu' where permission_name = 'import data'");
$this->db->query("update permissions set description = 'The user has access to the data submenu' where permission_name = 'data menu'");
$this->db->query("update permissions set description = 'The user has access to the reports submenu' where permission_name = 'reports menu'");
$this->db->query("update permissions set description = 'The user has access to the recordings panel within a campaign' where permission_name = 'search recordings'");
$this->db->query("update permissions set description = 'The user can see the urgent dropdown menu on the record update panel' where permission_name = 'urgent dropdown'");
$this->db->query("update permissions set description = 'The user can set a record as urgent by clicking the flag on the record update panel' where permission_name = 'urgent flag'");
$this->db->query("update permissions set description = 'The user has access to the survey answers report' where permission_name = 'survey answers'");
$this->db->query("update permissions set description = 'The user has access to the transfer reports' where permission_name = 'Transfers'");
$this->db->query("update permissions set description = 'The user has access to the activity reports' where permission_name = 'activity'");
$this->db->query("update permissions set description = 'The user has access to the outcomes reports' where permission_name = 'outcomes'");
$this->db->query("update permissions set description = 'The user has access to the email reports' where permission_name = 'email'");
$this->db->query("update permissions set description = 'The user can filter reports by team' where permission_name = 'by team'");
$this->db->query("update permissions set description = 'The user can filter reports by group' where permission_name = 'by group'");
$this->db->query("update permissions set description = 'The user can only report on their own team members' where permission_name = 'view own team'");
$this->db->query("update permissions set description = 'The user can search records assigned to any team' where permission_name = 'search teams'");
$this->db->query("update permissions set description = 'The user can only see data assigned to their own group by default' where permission_name = 'view own group'");
$this->db->query("update permissions set description = 'The user can only see data assigend to them by default' where permission_name = 'view own records'");
$this->db->query("update permissions set description = 'The user can search dead records' where permission_name = 'search dead'");
$this->db->query("update permissions set description = 'The user can search for records assigned to any group' where permission_name = 'search groups'");
$this->db->query("update permissions set description = 'The user can search for records assigned to any other user' where permission_name = 'search any owner'");
$this->db->query("update permissions set description = 'The user can search for records unassigned records' where permission_name = 'search unassigned'");
$this->db->query("update permissions set description = 'The user can search parked records' where permission_name = 'search parked'");
$this->db->query("update permissions set description = 'The user can view records in multiple campaigns from the list views' where permission_name = 'mix campaigns'");
$this->db->query("update permissions set description = 'The user has access to the NBF Dashboard' where permission_name = 'nbf dash'");
$this->db->query("update permissions set description = 'The user can filter reports by agent' where permission_name = 'by agent'");
$this->db->query("update permissions set description = 'The user can delete emails on a record' where permission_name = 'delete email'");
$this->db->query("update permissions set description = 'The user can view the mini-calendar from the appointments panel' where permission_name = 'mini calendar'");
$this->db->query("update permissions set description = 'The user has access to the full page calendar' where permission_name = 'full calendar'");
$this->db->query("update permissions set description = 'The user can add attachments to a record' where permission_name = 'add attachment'");
$this->db->query("update permissions set description = 'The user can view attachements on a record' where permission_name = 'view attachments'");
$this->db->query("update permissions set description = 'The user has access to the campaign submenu' where permission_name = 'campaign menu'");
$this->db->query("update permissions set description = 'The user has access to the admin submenu' where permission_name = 'admin menu'");
$this->db->query("update permissions set description = 'The user can see the footer info display showing basic stats.' where permission_name = 'show footer'");
$this->db->query("update permissions set description = 'The user can view agent hours' where permission_name = 'view hours'");
$this->db->query("update permissions set description = 'The user has access to various system logs' where permission_name = 'view logs'");
$this->db->query("update permissions set description = 'The user has access to the data management page allowing them to reallocate data between users' where permission_name = 'reassign data'");
$this->db->query("update permissions set description = 'The user can add/edit email templates' where permission_name = 'edit templates'");
$this->db->query("update permissions set description = 'The user can add/edit campaign scripts' where permission_name = 'edit scripts'");
$this->db->query("update permissions set description = 'The user can add/edit agent hours' where permission_name = 'log hours'");
$this->db->query("update permissions set description = 'The view the survey list page with filters' where permission_name = 'search surveys'");
$this->db->query("update permissions set description = 'The user can search records by campaign' where permission_name = 'search campaigns'");
$this->db->query("update permissions set description = 'The user has access to the management dashboard' where permission_name = 'management dash'");
$this->db->query("update permissions set description = 'The user has access to the dlient dashboard' where permission_name = 'client dash'");
$this->db->query("update permissions set description = 'The user has access to the agent dashboard' where permission_name = 'agent dash'");
$this->db->query("update permissions set description = 'The user can access any and all campaigns on the system' where permission_name = 'all campaigns'");
$this->db->query("update permissions set description = 'The user can see the email panel on the record page' where permission_name = 'view email'");
$this->db->query("update permissions set description = 'The user can send emails from the record page' where permission_name = 'send email'");
$this->db->query("update permissions set description = 'The user has access to the search page' where permission_name = 'search records'");
$this->db->query("update permissions set description = 'The user can delete recordings (n/a)' where permission_name = 'delete recordings'");
$this->db->query("update permissions set description = 'The user can vie wthe records associated to a record' where permission_name = 'view recordings'");
$this->db->query("update permissions set description = 'The user can edit the history on a record' where permission_name = 'edit history'");
$this->db->query("update permissions set description = 'The user can delete the history on a record' where permission_name = 'delete history'");
$this->db->query("update permissions set description = 'The user can view the history panel on a record' where permission_name = 'view history'");
$this->db->query("update permissions set description = 'The user can cancel appointments on the system' where permission_name = 'delete appointments'");
$this->db->query("update permissions set description = 'The user can edit appointments on the system' where permission_name = 'edit appointments'");
$this->db->query("update permissions set description = 'The user can add appointments to the system' where permission_name = 'add appointments'");
$this->db->query("update permissions set description = 'The user can view appointments on the system' where permission_name = 'view appointments'");
$this->db->query("update permissions set description = 'The user can change the ownership from within a  record' where permission_name = 'change ownership'");
$this->db->query("update permissions set description = 'The user can view the ownership panel' where permission_name = 'view ownership'");
$this->db->query("update permissions set description = 'The user can park/unpark a record' where permission_name = 'park records'");
$this->db->query("update permissions set description = 'The can reset a record. Bringing a dead record back in for dialling' where permission_name = 'reset records'");
$this->db->query("update permissions set description = 'The user can add new recors to the system' where permission_name = 'add records'");
$this->db->query("update permissions set description = 'The user can edit companies on a record' where permission_name = 'edit companies'");
$this->db->query("update permissions set description = 'The user can add companies to a record' where permission_name = 'add companies'");
$this->db->query("update permissions set description = 'The user can delete contacts from a record' where permission_name = 'delete contacts'");
$this->db->query("update permissions set description = 'The user can edit contacts on a  record' where permission_name = 'edit contacts'");
$this->db->query("update permissions set description = 'The user can add contacts to a record' where permission_name = 'add contacts'");
$this->db->query("update permissions set description = 'The user can delete surveys from a record' where permission_name = 'delete surveys'");
$this->db->query("update permissions set description = 'The user can edit surveys on a record' where permission_name = 'edit surveys'");
$this->db->query("update permissions set description = 'The user can view surveys on a record' where permission_name = 'view surveys'");
$this->db->query("update permissions set description = 'The user can add surveys to a record' where permission_name = 'add surveys'");
$this->db->query("update permissions set description = 'The user can set a progress status on a record. ie. Pending, In progress, Completed' where permission_name = 'set progress'");
$this->db->query("update permissions set description = 'The user can set a call outcome on a record. ie. Answer machine, Sale etc' where permission_name = 'set call outcomes'");
}
}
?>