--
-- Dumping data for table `Permission`
--

INSERT INTO `Permission` (`name`, `description`, `category`, `createdAt`, `updatedAt`, `createdById`, `updatedById`) VALUES
('Dashboard.Dashboard', 'Dashboard', 'Dashboard', '2016-01-19 03:11:20', NULL, -1, NULL),
('Permission.Create', 'Permission create', 'Permission', '0000-00-00 00:00:00', '2016-01-19 03:14:22', -1, -1),
('Permission.Delete', 'Permission delete', 'Permission', '0000-00-00 00:00:00', '2016-01-19 03:14:13', -1, -1),
('Permission.Index', 'Permission manage', 'Permission', '0000-00-00 00:00:00', '2016-01-19 03:14:28', -1, -1),
('Permission.Update', 'Permission update', 'Permission', '0000-00-00 00:00:00', '2016-01-19 03:14:06', -1, -1),
('Permission.View', 'Permission view', 'Permission', '0000-00-00 00:00:00', '2016-01-19 03:14:34', -1, -1),
('Property.Index', 'Property manage', 'Property', '2016-01-19 03:19:09', '2016-01-19 03:20:01', -1, -1),
('Property.View', 'Property view', 'Property', '2016-01-19 03:19:48', NULL, -1, NULL),
('Role.Create', 'Role create', 'Role', '0000-00-00 00:00:00', '2016-01-19 03:13:48', -1, -1),
('Role.Delete', 'Role delete', 'Role', '0000-00-00 00:00:00', '2016-01-19 03:13:40', -1, -1),
('Role.Index', 'Role manage', 'Role', '0000-00-00 00:00:00', '2016-01-19 03:13:54', -1, -1),
('Role.Update', 'Role update', 'Role', '0000-00-00 00:00:00', '2016-01-19 03:13:32', -1, -1),
('Role.View', 'Role view', 'Role', '0000-00-00 00:00:00', '2016-01-19 03:14:00', -1, -1),
('User.ChangePassword', 'Change password', 'System user', '0000-00-00 00:00:00', '2016-01-19 03:16:10', -1, -1),
('User.Create', 'System user create', 'System user', '0000-00-00 00:00:00', '2016-01-19 03:15:26', -1, -1),
('User.Delete', 'System user delete', 'System user', '0000-00-00 00:00:00', '2016-01-19 03:15:36', -1, -1),
('User.Index', 'System user manage', 'System user', '0000-00-00 00:00:00', '2016-01-19 03:15:17', -1, -1),
('User.MyAccount', 'My account', 'System user', '0000-00-00 00:00:00', '2016-01-19 03:16:02', -1, -1),
('User.RegUserIndex', 'User manage', 'User', '2016-01-19 03:17:16', NULL, -1, NULL),
('User.RegUserView', 'View user', 'User', '2016-01-19 03:18:09', NULL, -1, NULL),
('User.Update', 'System user update', 'System user', '0000-00-00 00:00:00', '2016-01-19 03:15:44', -1, -1),
('User.View', 'System user view', 'System user', '0000-00-00 00:00:00', '2016-01-19 03:15:04', -1, -1);

--
-- Dumping data for table `Role`
--

INSERT INTO `Role` (`name`, `description`, `createdAt`, `updatedAt`, `createdById`, `updatedById`) VALUES
('Administrator', 'Administrator Role', '2015-11-17 00:46:30', '2015-12-07 03:09:00', -1, -1),
('SuperAdmin', 'Super sdministrator users Role', '0000-00-00 00:00:00', NULL, -1, NULL);

--
-- Dumping data for table `RolePermission`
--

INSERT INTO `RolePermission` (`roleName`, `permissionName`, `createdAt`, `updatedAt`, `createdById`, `updatedById`) VALUES
('Administrator', 'Dashboard.Dashboard', '2016-01-19 03:55:29', NULL, -1, NULL),
('Administrator', 'Property.Index', '2016-01-19 03:55:29', NULL, -1, NULL),
('Administrator', 'Property.View', '2016-01-19 03:55:29', NULL, -1, NULL),
('Administrator', 'Role.Create', '2016-01-19 03:55:29', NULL, -1, NULL),
('Administrator', 'Role.Delete', '2016-01-19 03:55:29', NULL, -1, NULL),
('Administrator', 'Role.Index', '2016-01-19 03:55:29', NULL, -1, NULL),
('Administrator', 'Role.Update', '2016-01-19 03:55:29', NULL, -1, NULL),
('Administrator', 'Role.View', '2016-01-19 03:55:29', NULL, -1, NULL),
('Administrator', 'User.ChangePassword', '2016-01-19 03:55:29', NULL, -1, NULL),
('Administrator', 'User.Create', '2016-01-19 03:55:29', NULL, -1, NULL),
('Administrator', 'User.Delete', '2016-01-19 03:55:29', NULL, -1, NULL),
('Administrator', 'User.Index', '2016-01-19 03:55:29', NULL, -1, NULL),
('Administrator', 'User.MyAccount', '2016-01-19 03:55:29', NULL, -1, NULL),
('Administrator', 'User.RegUserIndex', '2016-01-19 03:55:29', NULL, -1, NULL),
('Administrator', 'User.RegUserView', '2016-01-19 03:55:29', NULL, -1, NULL),
('Administrator', 'User.Update', '2016-01-19 03:55:29', NULL, -1, NULL),
('Administrator', 'User.View', '2016-01-19 03:55:29', NULL, -1, NULL);

--
-- Dumping data for table `User`
--

INSERT INTO `User` (`id`, `username`, `password`, `firstName`, `lastName`, `email`, `timeZone`, `roleName`, `type`, `status`, `fbId`, `fbAccessToken`, `gplusId`, `gplusAccessToken`, `linkedInId`, `linkedInAccessToken`, `phone`, `userToken`, `createdAt`, `updatedAt`, `createdById`, `updatedById`, `bankAccountNo`, `bankName`, `profileImage`, `language`, `idImage`, `taxFile`, `dob`, `lastAccess`, `bankAccountName`, `iban`, `swift`, `rating`) VALUES
(-1, 'superadmin', '$1$gm..pp5.$jvLbhB.c6VWsw6R8OdMD20', 'Super', 'Administrator', 'aruna.470@gmail.com', 'Asia/Colombo', 'SuperAdmin', 3, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0000-00-00 00:00:00', '2015-12-21 11:02:40', -1, -1, NULL, NULL, NULL, NULL, 'National identiy', 'Tax file', NULL, NULL, NULL, NULL, NULL, 0),
(2, 'admin', '$1$8X4.vc3.$ZOMxlEBgyhc.mV4f82Bd.0', 'Administrator', NULL, 'admin@gmail.com', 'Asia/Colombo', 'Administrator', 3, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2016-01-19 03:46:01', NULL, -1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

