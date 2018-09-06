--- 2016-01-19 ---
ALTER TABLE `User` CHANGE `rating` `rating` FLOAT NULL DEFAULT '0' COMMENT 'Overall user rating';

ALTER TABLE `PaymentCard` DROP `token`;

ALTER TABLE `PaymentCard`
  ADD `cardType` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL COMMENT 'visa,mastercard etc..' AFTER `expire`,
  ADD `adyenPspReference` VARCHAR(25) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL COMMENT 'pspreference value sent by Adyen gateway' AFTER `cardType`,
  ADD `adyenShopperReference` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL COMMENT 'Unique reference that assigned when creating a recurring payment' AFTER `adyenPspReference`;

RENAME TABLE PaymentCard TO PaymentPlanInfo;
RENAME TABLE PaymentPlanInfo TO PaymentPlan;

ALTER TABLE `PaymentPlan` ADD `paymentGateway` TINYINT(2) NOT NULL COMMENT '1 - Adyen' AFTER `adyenShopperReference`;

--- 2016-01-20 ---
ALTER TABLE `PaymentPlan`
  ADD `cardHolderName` VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL AFTER `cardType`,
  ADD `cardNumber` VARCHAR(6) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL AFTER `cardHolderName`;

ALTER TABLE `PaymentPlan` ADD `createdAt` DATETIME NOT NULL AFTER `paymentGateway`;

ALTER TABLE `PaymentPlan` ADD `adyenRecurringDetailReference` VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL COMMENT 'Adyen recurring contract reference' AFTER `adyenShopperReference`;

--- 2016-01-21 ---
CREATE TABLE `Verification` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `verificationCode` int(4) NOT NULL COMMENT '4 digits verification code',
 `phoneNumber` varchar(20) COLLATE utf8_unicode_ci NOT NULL COMMENT 'recipient mobile number',
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci

--- 2016-01-22 ---
ALTER TABLE `User` ADD `sysEmail` VARCHAR(60) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT 'Email for system user' AFTER `email`;
ALTER TABLE `Property` ADD `keyMoney` FLOAT NULL DEFAULT NULL COMMENT 'Keymoney in USD' AFTER `chargingCycle`;
ALTER TABLE `PropertyRequest` ADD `payKeyMoneyCc` TINYINT(1) NOT NULL DEFAULT '0' COMMENT 'Pay keymoney via CC. 0-No, 1-Yes' AFTER `bookingDuration`;

--- 2016-01-24 ---
ALTER TABLE `Payment` CHANGE `tenantUserId` `userId` INT(11) NOT NULL;
ALTER TABLE `Payment` ADD `type` TINYINT(1) NOT NULL COMMENT '1-keymoney payment,2-monthly rental 2' AFTER `amount`;
ALTER TABLE `Payment` ADD `adyenPspReference` VARCHAR(30) NULL DEFAULT NULL COMMENT 'pspReference value retruned from Adyen API' AFTER `type`;
ALTER TABLE `Payment` ADD `adyenTransactionReference` VARCHAR(20) NULL DEFAULT NULL COMMENT 'Uniqe id assigned for a Adyen transaction' AFTER `adyenPspReference`;
ALTER TABLE `Property` CHANGE `chargingStartDate` `reservedAt` DATETIME NULL DEFAULT NULL COMMENT 'Property reserved date time';

--- 2016-01-26 ---
ALTER TABLE `Property` ADD `city` VARCHAR(25) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT 'City name' AFTER `keyMoney`;
ALTER TABLE `Property` ADD `payDay` INT(2) NULL DEFAULT NULL COMMENT 'Payment date of the month' AFTER `city`;
ALTER TABLE `Property` ADD `chargingAttemptCount` INT(1) NULL DEFAULT NULL COMMENT 'Charging attempt count' AFTER `city`;
ALTER TABLE `Property` CHANGE `chargingAttemptCount` `chargingAttemptCount` INT(1) NULL DEFAULT '0' COMMENT 'Charging attempt count';
ALTER TABLE `Property` CHANGE `chargingAttemptCount` `chargingAttemptCount` INT(1) NOT NULL DEFAULT '0' COMMENT 'Charging attempt count';
ALTER TABLE `Property` CHANGE `chargingAttemptCount` `chargingAttemptCount` INT(1) NULL DEFAULT '0' COMMENT 'Charging attempt count';

--- 2016-01-28 ---
ALTER TABLE `User` CHANGE `gplusId` `gplusId` VARCHAR(35) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;
ALTER TABLE `Property` ADD `nextChargingAttemptDate` DATE NULL DEFAULT NULL COMMENT 'Next charging attempt date' AFTER `chargingAttemptCount`;
ALTER TABLE `Payment` ADD `payeeUserId` INT NOT NULL COMMENT 'person who receives the payment' AFTER `userId`;
ALTER TABLE `Payment` CHANGE `userId` `payerUserId` INT(11) NOT NULL COMMENT 'Person who makes the payment';

--- 2016-01-29 ---
ALTER TABLE `Payment` ADD `paymentForDate` DATE NULL DEFAULT NULL COMMENT 'This payment is for whic date' AFTER `createdAt`;
INSERT INTO `Permission` (`name`, `description`, `category`, `createdAt`, `updatedAt`, `createdById`, `updatedById`) VALUES
('Payment.Index', 'Manage payments', 'Payments', '2016-01-29 09:01:27', NULL, -1, NULL);
INSERT INTO `RolePermission` (`roleName`, `permissionName`, `createdAt`, `updatedAt`, `createdById`, `updatedById`) VALUES
('Administrator', 'Payment.Index', '2016-01-29 09:01:54', NULL, -1, NULL);
ALTER TABLE `User` ADD `passwordResetToken` VARCHAR(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL COMMENT 'Temparly token generated for password';
ALTER TABLE `User` CHANGE `passwordResetToken` `passwordResetToken` VARCHAR(25) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT 'Temparly token generated for password';

--- 2016-02-01 ---
ALTER TABLE `Property` ADD `lastPaymentDate` DATETIME NULL DEFAULT NULL COMMENT 'Last payment date.' AFTER `nextChargingAttemptDate`;

--- 2016-02-02 ---
ALTER TABLE `Property` CHANGE `size` `size` FLOAT NULL DEFAULT NULL COMMENT 'size in square feet';

--- 2016-02-08 ---
ALTER TABLE `Property` CHANGE `nextChargingAttemptDate` `nextChargingAttemptDate` DATETIME NULL DEFAULT NULL COMMENT 'Next charging attempt date';
ALTER TABLE `Property` CHANGE `nextChargingDate` `nextChargingDate` DATETIME NULL DEFAULT NULL;
ALTER TABLE `Property` ADD `isRecurringStopped` TINYINT(1) NULL DEFAULT NULL COMMENT '1 - not stop, 2 - stopped' AFTER `lastPaymentDate`;
ALTER TABLE `Property` CHANGE `isRecurringStopped` `isRecurring` TINYINT(1) NULL DEFAULT NULL COMMENT '1 - not stop, 2 - stopped';
ALTER TABLE `Property` CHANGE `isRecurring` `reachMaxAttempts` TINYINT(1) NULL DEFAULT NULL COMMENT '1 - Yes, 2 - No';
ALTER TABLE `Property` CHANGE `reachMaxAttempts` `reachMaxAttempts` TINYINT(1) NULL DEFAULT NULL COMMENT '2 - Yes, 1 - No';

ALTER TABLE `PropertyHistory` CHANGE `fromDate` `fromDate` DATETIME NOT NULL;
ALTER TABLE `PropertyHistory` CHANGE `toDate` `toDate` DATETIME NOT NULL;

--- 2016-02-12 ---
ALTER TABLE `Payment` ADD `currencyType` VARCHAR(5) NULL DEFAULT NULL COMMENT 'Currency code' AFTER `amount`;

--- 2016-02-16 ---
ALTER TABLE `Property` ADD `thumbImageName` VARCHAR(40) NULL DEFAULT NULL COMMENT 'Thumbnail image name' AFTER `imageName`;
ALTER TABLE `User`  ADD `profileImageThumb` VARCHAR(40) NULL DEFAULT NULL COMMENT 'Thumbnail image name of profile image'  AFTER `profileImage`;

--- 2016-02-17 ---
ALTER TABLE `User` CHANGE `language` `language` VARCHAR(8) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;

--- 2016-02-19 ---
INSERT INTO `Permission` (`name`, `description`, `category`, `createdAt`, `updatedAt`, `createdById`, `updatedById`) VALUES
('User.RegUserUpdate', 'Update User', 'User', '2016-02-19 06:27:40', NULL, -1, NULL);

INSERT INTO `RolePermission` (`roleName`, `permissionName`, `createdAt`, `updatedAt`, `createdById`, `updatedById`) VALUES
('Administrator', 'User.RegUserUpdate', '2016-02-19 06:28:45', NULL, -1, NULL);

--- 2016-02-24 ---
ALTER TABLE `Payment` ADD `commssion` FLOAT NULL DEFAULT NULL COMMENT 'Commission amount' AFTER `paymentForDate`, ADD `percentage` FLOAT NULL DEFAULT NULL COMMENT 'Commission percentage' AFTER `commssion`;

--- 2016-02-25 ---
ALTER TABLE `Verification` ADD INDEX(`phoneNumber`);

--- 2016-02-26 ---
CREATE TABLE `AdyenNotifications` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `originalReference` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
 `reason` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
 `merchantAccountCode` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
 `eventCode` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
 `success` tinyint(1) DEFAULT NULL,
 `paymentMethod` varchar(5) COLLATE utf8_unicode_ci DEFAULT NULL,
 `currency` varchar(5) COLLATE utf8_unicode_ci DEFAULT NULL,
 `pspReference` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
 `merchantReference` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
 `value` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
 `eventDate` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
 `createdAt` datetime DEFAULT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci

RENAME TABLE `AdyenNotifications` TO `AdyenNotification`;
ALTER TABLE `AdyenNotification` CHANGE `success` `success` VARCHAR(6) NULL DEFAULT NULL;

ALTER TABLE `AdyenNotification` ADD `live` VARCHAR(6) NULL AFTER `eventDate`;

--- 2016-03-23 ---
ALTER TABLE `PaymentPlan` ADD `stripeCustomerId` VARCHAR(25) NULL DEFAULT NULL AFTER `paymentGateway`, ADD `stripeCardId` VARCHAR(35) NULL DEFAULT NULL AFTER `stripeCustomerId`;
ALTER TABLE `Payment` ADD `stripeReference` VARCHAR(35) NULL DEFAULT NULL COMMENT 'Stripe charging reference' AFTER `percentage`;


----------------------------------------- V 1.1 ------------------------------------------------
--- 2016-04-01 ---
ALTER TABLE `Property` ADD `commissionPlan` TINYINT(2) NOT NULL COMMENT '1 - Renter, 2 - Owner, 3 - Split' AFTER `reachMaxAttempts`;

--- 2016-04-04 ---
ALTER TABLE `Property` ADD `duration` INT(2) NULL DEFAULT NULL COMMENT 'Property rented out duration in months' AFTER `commissionPlan`;
ALTER TABLE `Property` ADD `isOnBhf` TINYINT(1) NOT NULL DEFAULT '0' COMMENT 'On behalf of property. 1 - yes, 0 - no' AFTER `duration`;

--- 2016-04-05 ---
ALTER TABLE `User` ADD `isOnBhf` TINYINT(1) NOT NULL DEFAULT '0' COMMENT 'Whether on behalf of user. 1 - Yes, 0 - N0' AFTER `passwordResetToken`;
ALTER TABLE `Property` CHANGE `commissionPlan` `commissionPlan` TINYINT(2) NOT NULL DEFAULT '1' COMMENT '1 - Renter, 2 - Owner, 3 - Split';

----------------------------------------- V 1.2 ------------------------------------------------
--- 2016-04-26 ---
ALTER TABLE `User` ADD `profDes` VARCHAR(140) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT 'Profile description' AFTER `isOnBhf`;

--- 2016-04-27 ---
ALTER TABLE `Property` ADD `images` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT 'Property images in json format' AFTER `isOnBhf`;

--- 2016-04-28 ---
ALTER TABLE `User` CHANGE `profDes` `profDes` MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT 'Profile description';
ALTER TABLE `Property` CHANGE `description` `description` MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;
ALTER TABLE `User` ADD `companyRegNum` VARCHAR(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT 'Company registration number' AFTER `profDes`;
ALTER TABLE `Property` CHANGE `name` `name` VARCHAR(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;

--- 2016-04-29 ---
ALTER TABLE `User` CHANGE `profDes` `profDes` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT 'Profile description';
ALTER TABLE `Property` CHANGE `description` `description` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;

--- 2016-05-09 ---
ALTER TABLE `User` ADD `companyType` TINYINT(1) NULL DEFAULT NULL COMMENT '1 - Real state agency, 2 - Property management, 3 - Building management' AFTER `companyRegNum`;
ALTER TABLE `User` CHANGE `companyType` `companyType` TINYINT(1) NULL DEFAULT '0' COMMENT '0 - Personal, 1 - Real state agency, 2 - Property management, 3 - Building management';

--- 2016-05-12 ---
ALTER TABLE `User` ADD `companyName` VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL AFTER `companyType`;

----------------------------------------- V 1.3 - MangoPay integration ------------------------------------------------
CREATE TABLE `CompanyWallet` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `email` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
 `firstName` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
 `lastName` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
 `birthdate` datetime NOT NULL,
 `nationality` varchar(3) COLLATE utf8_unicode_ci NOT NULL,
 `countryOfResidence` varchar(3) COLLATE utf8_unicode_ci NOT NULL,
 `incomeRange` int(11) DEFAULT NULL,
 `occupation` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
 `createdAt` datetime NOT NULL,
 `updatedAt` timestamp NOT NULL,
 `createdById` int(11) NOT NULL,
 `mpUserId` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
 `mpWalletId` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `CompanyWallet` CHANGE `updatedAt` `updatedAt` DATETIME NULL DEFAULT NULL;

ALTER TABLE `CompanyWallet` ADD `address` TEXT NOT NULL AFTER `mpWalletId`;

ALTER TABLE `CompanyWallet` ADD `kycDocuments` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT 'JSON string of KYC documents.' AFTER `address`;

--- 2016-05-25 ---
CREATE TABLE `CompanyWireIn` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `wireReference` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
 `type` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
 `ownerName` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
 `ownerAddress` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
 `bic` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
 `iban` varchar(35) COLLATE utf8_unicode_ci NOT NULL,
 `amount` float NOT NULL,
 `currency` varchar(4) COLLATE utf8_unicode_ci NOT NULL,
 `status` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
 `mpWalletId` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
 `mpUserId` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
 `createdAt` datetime NOT NULL,
 `createdById` int(11) NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE CompanyWireIn ADD CONSTRAINT fk_comp_wire_in_user FOREIGN KEY (createdById) REFERENCES User(id);

ALTER TABLE `CompanyWireIn` ADD `mpWireInId` VARCHAR(15) NOT NULL AFTER `createdById`;

ALTER TABLE `CompanyWireIn` CHANGE `mpWireInId` `mpPayInId` VARCHAR(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;

--- 2016-05-26 ---
RENAME TABLE `CompanyWireIn` TO CompanyPayIn`;

ALTER TABLE `User` ADD `occupation` VARCHAR(60) NULL DEFAULT NULL AFTER `companyName`, ADD `incomeRange` INT(2) NULL DEFAULT NULL COMMENT 'MangoPay income range identifier' AFTER `occupation`, ADD `countryOfResidence` VARCHAR(3) NULL DEFAULT NULL COMMENT 'User''s residential country code' AFTER `incomeRange`;
ALTER TABLE `User` ADD `nationality` VARCHAR(3) NULL DEFAULT NULL COMMENT 'User''s nationality' AFTER `countryOfResidence`;

CREATE TABLE `UserMpInfo` (
 `userId` int(11) NOT NULL,
 `mpUserId` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'User''s MangoPay user account id',
 `mpWalletId` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'User''s MangoPay wallet id',
 `mpBankAccountId` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'User''s MangoPay bank account id',
 PRIMARY KEY (`userId`),
 CONSTRAINT `fk_user_mp_info_user` FOREIGN KEY (`userId`) REFERENCES `User` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `User` ADD `address` TINYTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL AFTER `nationality`;

ALTER TABLE `User` DROP `address`;
ALTER TABLE `User` DROP `nationality`;
ALTER TABLE `User` DROP `countryOfResidence`;
ALTER TABLE `User` DROP `incomeRange`;
ALTER TABLE `User` DROP `occupation`;

CREATE TABLE `UserMpInfo` (
 `userId` int(11) NOT NULL,
 `mpUserId` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'User''s MangoPay user account id',
 `mpWalletId` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'User''s MangoPay wallet id',
 `mpBankAccountId` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'User''s MangoPay bank account id',
 PRIMARY KEY (`userId`),
 CONSTRAINT `fk_user_mp_info_user` FOREIGN KEY (`userId`) REFERENCES `User` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE UserMpInfo;

CREATE TABLE `UserMpInfo` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `userId` int(11) NOT NULL,
 `mpUserId` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
 `mpWalletId` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
 `mpBankAccountId` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
 `address` tinytext COLLATE utf8_unicode_ci NOT NULL,
 `nationality` varchar(3) COLLATE utf8_unicode_ci NOT NULL,
 `countryOfResidence` varchar(3) COLLATE utf8_unicode_ci NOT NULL,
 `incomeRange` int(11) NOT NULL,
 `occupation` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
 `createdAt` datetime NOT NULL,
 `updatedAt` datetime DEFAULT NULL,
 PRIMARY KEY (`id`),
 KEY `fk_user_mp_info_user` (`userId`),
 CONSTRAINT `fk_user_mp_info_user` FOREIGN KEY (`userId`) REFERENCES `User` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `UserMpInfo` ADD `email` VARCHAR(60) NOT NULL , ADD `firstName` VARCHAR(30) NOT NULL , ADD `lastName` VARCHAR(60) NOT NULL ;
ALTER TABLE `UserMpInfo` ADD `birthdate` DATE NOT NULL AFTER `lastName`;
ALTER TABLE `UserMpInfo` CHANGE `birthdate` `birthDate` DATE NOT NULL;
ALTER TABLE `CompanyWallet` CHANGE `birthdate` `birthDate` DATE NOT NULL;
ALTER TABLE `UserMpInfo` ADD UNIQUE(`userId`);

--- 2016-05-27 ---
ALTER TABLE `UserMpInfo` ADD `iban` VARCHAR(60) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL AFTER `birthDate`;

CREATE TABLE `UserMpInfoFile` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `userMpInfoId` int(11) NOT NULL,
 `userId` int(11) NOT NULL,
 `name` int(11) NOT NULL,
 `type` int(11) NOT NULL,
 `status` int(11) NOT NULL,
 `mpDocId` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'MangoPay reference id',
 `createdAt` datetime NOT NULL,
 `updatedAt` datetime NOT NULL,
 PRIMARY KEY (`id`),
 KEY `fk_user_mp_info_file_user` (`userId`),
 KEY `fk_user_mp_info_file_user_mp_info` (`userMpInfoId`),
 CONSTRAINT `fk_user_mp_info_file_user` FOREIGN KEY (`userId`) REFERENCES `User` (`id`),
 CONSTRAINT `fk_user_mp_info_file_user_mp_info` FOREIGN KEY (`userMpInfoId`) REFERENCES `UserMpInfo` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--- 2016-05-28 ---
ALTER TABLE `UserMpInfo` ADD `city` VARCHAR(60) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL AFTER `iban`, ADD `postalCode` VARCHAR(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL AFTER `city`;

--- 2016-05-29 ---
ALTER TABLE `UserMpInfoFile` CHANGE `name` `fileName` VARCHAR(30) NOT NULL;

--- 2016-05-30 ---
ALTER TABLE `UserMpInfoFile` CHANGE `type` `type` TINYINT(2) NOT NULL;

CREATE TABLE `MpPayout` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `paymentId` int(11) NOT NULL,
 `mpTransferId` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
 `mpTransferStatus` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
 `userId` int(11) NOT NULL,
 `mpPayoutId` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
 `mpPayoutStatus` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
 `createdAt` datetime NOT NULL,
 `mpBankAccountId` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
 `mpPayoutExecutionDate` int(11) DEFAULT NULL,
 `retryCount` int(2) NOT NULL DEFAULT '0',
 `failedReasonCode` tinyint(2) DEFAULT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


RENAME TABLE `MpPayout` TO `Payout`;

ALTER TABLE `Payout` CHANGE `mpTransferStatus` `mpTransferStatus` VARCHAR(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;

--- 2016-05-31 ---
ALTER TABLE `Payment` ADD `isPayoutProcessed` TINYINT(1) NOT NULL DEFAULT '0' COMMENT 'Whether payout processed. 1 - Yes, 0-No' AFTER `stripeReference`;
ALTER TABLE `Payout` ADD `mpTransferMessage` TINYTEXT NULL DEFAULT NULL COMMENT 'Walltet to wallet transfer fail message' AFTER `failedReasonCode`, ADD `mpPayoutMessage` TINYTEXT NULL DEFAULT NULL COMMENT 'Wallet to bank account trnsfer fail message' AFTER `mpTransferMessage`;

ALTER TABLE Payout ADD CONSTRAINT fk_payout_user FOREIGN KEY (userId) REFERENCES User(id);
ALTER TABLE Payout ADD CONSTRAINT fk_payout_payment FOREIGN KEY (paymentId) REFERENCES Payment(id);

--- 2016-06-01 ---
ALTER TABLE `Payout` CHANGE `failedReasonCode` `eligibilityStatus` TINYINT(2) NULL DEFAULT NULL COMMENT '1 - Success,';
ALTER TABLE `Payout` CHANGE `eligibilityStatus` `eligibilityStatus` TINYINT(2) NULL DEFAULT NULL COMMENT '1 - Success, 2 - No Comapny Wallet, 3 - Bank details not provided';
ALTER TABLE `Payout` ADD `maxRetry` INT(2) NOT NULL COMMENT 'Maximum retries' AFTER `mpPayoutMessage`;

--- 2016-06-03 ---
ALTER TABLE `UserMpInfo` ADD UNIQUE( `userId`);
ALTER TABLE `UserMpInfoFile` CHANGE `updatedAt` `updatedAt` DATETIME NULL DEFAULT NULL;

--- 2016-06-06 ---
INSERT INTO `Permission` (`name`, `description`, `category`, `createdAt`, `updatedAt`, `createdById`, `updatedById`) VALUES
('CompanyWallet.Index', 'Manage Company Wallet', 'Finance', '2016-06-02 05:35:43', NULL, -1, NULL),
('CompanyWallet.ManageKycDocs', 'Manage Company Proof Documents', 'Finance', '2016-06-02 05:37:47', NULL, -1, NULL),
('CompanyWallet.Update', 'Update Company Wallet', 'Finance', '2016-06-02 05:38:59', NULL, -1, NULL),
('CompanyWallet.View', 'View Company Wallet Details', 'Finance', '2016-06-02 05:38:37', NULL, -1, NULL),
('Payout.Index', 'Mange Owner Payouts', 'Finance', '2016-06-02 05:40:53', NULL, -1, NULL),
('Payout.View', 'View Payout Details', 'Finance', '2016-06-02 05:41:41', NULL, -1, NULL);

INSERT INTO `RolePermission` (`roleName`, `permissionName`, `createdAt`, `updatedAt`, `createdById`, `updatedById`) VALUES
('Administrator', 'CompanyWallet.Index', '2016-06-02 05:42:28', NULL, -1, NULL),
('Administrator', 'CompanyWallet.ManageKycDocs', '2016-06-02 05:42:28', NULL, -1, NULL),
('Administrator', 'CompanyWallet.Update', '2016-06-02 05:42:28', NULL, -1, NULL),
('Administrator', 'CompanyWallet.View', '2016-06-02 05:42:28', NULL, -1, NULL),
('Administrator', 'Payout.Index', '2016-06-02 05:42:28', NULL, -1, NULL),
('Administrator', 'Payout.View', '2016-06-02 05:42:28', NULL, -1, NULL);

--- 2016-06-08 ---
INSERT INTO `Permission` (`name`, `description`, `category`, `createdAt`, `updatedAt`, `createdById`, `updatedById`) VALUES
('CompanyPayIn.Create', 'Create PayIn', 'Finance', '2016-06-08 03:32:13', NULL, -1, NULL),
('CompanyPayIn.Index', 'Manage PayIn', 'Finance', '2016-06-08 03:34:46', NULL, -1, NULL),
('CompanyPayIn.View', 'View PayIn', 'Finance', '2016-06-08 03:36:24', NULL, -1, NULL);

INSERT INTO `RolePermission` (`roleName`, `permissionName`, `createdAt`, `updatedAt`, `createdById`, `updatedById`) VALUES
('Administrator', 'CompanyPayIn.Create', '2016-06-08 03:36:36', NULL, -1, NULL),
('Administrator', 'CompanyPayIn.Index', '2016-06-08 03:36:36', NULL, -1, NULL),
('Administrator', 'CompanyPayIn.View', '2016-06-08 03:36:36', NULL, -1, NULL);

