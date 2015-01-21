<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Migration_update_5 extends CI_Migration
{

  public function __construct()
  {
    $this->load->model('Database_model');
  }

  public function up()
  {

    //Alter table suppression
    $this->db->query("delete from role_permissions");
    $this->db->query("delete from permissions");
    $this->db->query("INSERT INTO `permissions` (`permission_id`, `permission_name`, `permission_group`) VALUES
(1, 'set call outcomes', 'Records'),
(2, 'set progress', 'Records'),
(3, 'add surveys', 'Surveys'),
(4, 'view surveys', 'Surveys'),
(5, 'edit surveys', 'Surveys'),
(6, 'delete surveys', 'Surveys'),
(7, 'add contacts', 'Contacts'),
(8, 'edit contacts', 'Contacts'),
(9, 'delete contacts', 'Contacts'),
(10, 'add companies', 'Companies'),
(11, 'edit companies', 'Companies'),
(12, 'add records', 'Records'),
(13, 'reset records', 'Records'),
(14, 'park records', 'Records'),
(15, 'view ownership', 'Ownership'),
(16, 'change ownership', 'Ownership'),
(17, 'view appointments', 'Appointments'),
(18, 'add appointments', 'Appointments'),
(19, 'edit appointments', 'Appointments'),
(20, 'delete appointments', 'Appointments'),
(21, 'view history', 'History'),
(22, 'delete history', 'History'),
(23, 'edit history', 'History'),
(24, 'view recordings', 'Recordings'),
(25, 'delete recordings', 'Recordings'),
(26, 'search records', 'Search'),
(27, 'send email', 'Email'),
(28, 'view email', 'Email'),
(29, 'all campaigns', 'System'),
(30, 'agent dash', 'Dashboards'),
(31, 'client dash', 'Dashboards'),
(32, 'management dash', 'Dashboards'),
(34, 'search campaigns', 'Search'),
(35, 'search surveys', 'Search'),
(36, 'log hours', 'System'),
(37, 'edit scripts', 'Admin'),
(38, 'edit templates', 'Admin'),
(39, 'reassign data', 'Data'),
(40, 'view logs', 'Admin'),
(41, 'view hours', 'Admin'),
(42, 'show footer', 'System'),
(43, 'admin menu', 'Admin'),
(44, 'campaign menu', 'Admin'),
(45, 'view attachments', 'Attachments'),
(46, 'add attachment', 'Attachments'),
(47, 'full calendar', 'Calendar'),
(48, 'mini calendar', 'Calendar'),
(49, 'delete email', 'Email'),
(52, 'by agent', 'Reports'),
(56, 'nbf dash', 'Dashboards'),
(57, 'mix campaigns', 'System'),
(59, 'search parked', 'Search'),
(60, 'search unassigned', 'Search'),
(61, 'search any owner', 'Search'),
(62, 'search groups', 'Search'),
(63, 'search dead', 'Search'),
(64, 'view own records', 'Default view'),
(65, 'view own group', 'Default view'),
(66, 'search teams', 'Search'),
(67, 'view own team', 'Default view'),
(69, 'by group', 'Reports'),
(70, 'by team', 'Reports'),
(71, 'email', 'Reports'),
(72, 'outcomes', 'Reports'),
(73, 'activity', 'Reports'),
(74, 'Transfers', 'Reports'),
(75, 'survey answers', 'Reports'),
(76, 'urgent flag', 'Records'),
(77, 'urgent dropdown', 'Records'),
(78, 'search recordings', 'Recordings'),
(79, 'reports menu', 'Reports'),
(80, 'data menu', 'Data'),
(81, 'import data', 'Data'),
(82, 'export data', 'Data'),
(83, 'archive data', 'Data'),
(84, 'ration data', 'Data'),
(85, 'use callpot', 'System'),
(86, 'view unassigned', 'Default view'),
(87, 'view parked', 'Default view'),
(88, 'view dead', 'Default view'),
(89, 'view completed', 'Default view'),
(90, 'view live', 'Default view'),
(91, 'view pending', 'Default view'),
(92, 'keep records', 'System'),
(93, 'use timer', 'System')");
    $this->db->query("INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 9),
(1, 10),
(1, 11),
(1, 12),
(1, 13),
(1, 14),
(1, 15),
(1, 16),
(1, 17),
(1, 18),
(1, 19),
(1, 20),
(1, 21),
(1, 22),
(1, 23),
(1, 24),
(1, 25),
(1, 26),
(1, 27),
(1, 28),
(1, 29),
(1, 30),
(1, 31),
(1, 32),
(1, 34),
(1, 35),
(1, 37),
(1, 38),
(1, 39),
(1, 40),
(1, 41),
(1, 43),
(1, 44),
(1, 45),
(1, 46),
(1, 47),
(1, 48),
(1, 49),
(1, 52),
(1, 56),
(1, 57),
(1, 58),
(1, 59),
(1, 60),
(1, 61),
(1, 62),
(1, 63),
(1, 66),
(1, 69),
(1, 70),
(1, 71),
(1, 72),
(1, 73),
(1, 74),
(1, 75),
(1, 76),
(1, 79),
(1, 80),
(1, 81),
(1, 82),
(1, 83),
(1, 84),
(1, 86),
(1, 90),
(2, 2),
(2, 3),
(2, 4),
(2, 5),
(2, 6),
(2, 7),
(2, 8),
(2, 9),
(2, 10),
(2, 11),
(2, 12),
(2, 13),
(2, 14),
(2, 15),
(2, 16),
(2, 17),
(2, 18),
(2, 19),
(2, 20),
(2, 21),
(2, 22),
(2, 23),
(2, 24),
(2, 25),
(2, 26),
(2, 27),
(2, 28),
(2, 29),
(2, 32),
(2, 34),
(2, 35),
(2, 38),
(2, 39),
(2, 40),
(2, 41),
(2, 43),
(2, 44),
(2, 47),
(2, 48),
(2, 49),
(2, 52),
(2, 58),
(2, 60),
(2, 61),
(2, 63),
(2, 67),
(3, 1),
(3, 3),
(3, 4),
(3, 5),
(3, 6),
(3, 7),
(3, 8),
(3, 9),
(3, 10),
(3, 11),
(3, 12),
(3, 13),
(3, 14),
(3, 15),
(3, 17),
(3, 18),
(3, 19),
(3, 20),
(3, 21),
(3, 22),
(3, 23),
(3, 24),
(3, 25),
(3, 27),
(3, 28),
(3, 30),
(3, 32),
(3, 35),
(3, 36),
(3, 40),
(3, 41),
(3, 43),
(3, 45),
(3, 46),
(3, 47),
(3, 48),
(3, 61),
(3, 63),
(3, 64),
(3, 66),
(3, 71),
(3, 72),
(3, 73),
(3, 79),
(3, 85),
(3, 90),
(4, 2),
(4, 3),
(4, 4),
(4, 5),
(4, 7),
(4, 8),
(4, 10),
(4, 11),
(4, 12),
(4, 13),
(4, 15),
(4, 16),
(4, 17),
(4, 18),
(4, 19),
(4, 21),
(4, 23),
(4, 26),
(4, 27),
(4, 28),
(4, 31),
(4, 45),
(4, 47),
(4, 60),
(4, 63),
(4, 64),
(4, 72),
(4, 73),
(4, 75),
(4, 76),
(4, 79),
(4, 89),
(5, 1),
(5, 3),
(5, 4),
(5, 5),
(5, 7),
(5, 8),
(5, 10),
(5, 11),
(5, 12),
(5, 13),
(5, 15),
(5, 17),
(5, 18),
(5, 19),
(5, 21),
(5, 27),
(5, 28),
(5, 30),
(5, 35),
(5, 36),
(5, 45),
(5, 46),
(5, 47),
(5, 48),
(5, 71),
(5, 72),
(5, 73),
(5, 79),
(5, 85),
(6, 2),
(6, 4),
(6, 5),
(6, 7),
(6, 8),
(6, 9),
(6, 10),
(6, 11),
(6, 12),
(6, 13),
(6, 14),
(6, 15),
(6, 16),
(6, 17),
(6, 18),
(6, 19),
(6, 20),
(6, 21),
(6, 22),
(6, 23),
(6, 24),
(6, 25),
(6, 26),
(6, 27),
(6, 28),
(6, 29),
(6, 30),
(6, 31),
(6, 32),
(6, 34),
(6, 35),
(6, 37),
(6, 38),
(6, 39),
(6, 40),
(6, 41),
(6, 43),
(6, 44),
(6, 45),
(6, 46),
(6, 47),
(6, 48),
(6, 52),
(6, 57),
(6, 58),
(6, 59),
(6, 60),
(6, 61),
(6, 62),
(6, 63),
(6, 66),
(6, 69),
(6, 70),
(6, 71),
(6, 72),
(6, 73),
(6, 74),
(6, 75),
(6, 78),
(6, 79),
(6, 89),
(6, 90),
(7, 1),
(7, 4),
(7, 8),
(7, 11),
(7, 15),
(7, 17),
(7, 21),
(7, 24),
(7, 26),
(7, 28),
(7, 29),
(7, 30),
(7, 31),
(7, 32),
(7, 34),
(7, 35),
(7, 37),
(7, 40),
(7, 43),
(7, 45),
(7, 47),
(7, 48),
(7, 52),
(7, 57),
(7, 59),
(7, 60),
(7, 61),
(7, 62),
(7, 63),
(7, 66),
(7, 69),
(7, 70),
(7, 71),
(7, 72),
(7, 73),
(7, 74),
(7, 75),
(7, 76),
(7, 79),
(8, 1),
(8, 7),
(8, 8),
(8, 9),
(8, 10),
(8, 11),
(8, 12),
(8, 13),
(8, 14),
(8, 15),
(8, 16),
(8, 17),
(8, 18),
(8, 19),
(8, 20),
(8, 21),
(8, 24),
(8, 25),
(8, 26),
(8, 27),
(8, 28),
(8, 30),
(8, 32),
(8, 34),
(8, 35),
(8, 38),
(8, 45),
(8, 46),
(8, 47),
(8, 48),
(8, 49),
(8, 52),
(8, 56),
(8, 58),
(8, 59),
(8, 60),
(8, 61),
(8, 62),
(8, 63),
(8, 64),
(8, 66),
(8, 71),
(8, 72),
(8, 73),
(8, 76),
(8, 79),
(8, 85),
(8, 90),
(8, 91),
(8, 92),
(5, 93)");

  }
  public function down()
  {
  
  }
}
