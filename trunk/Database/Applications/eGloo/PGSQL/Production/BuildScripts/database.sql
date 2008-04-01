-- Copyright 2008 eGloo, LLC
-- 
-- Licensed under the Apache License, Version 2.0 (the "License");
-- you may not use this file except in compliance with the License.
-- You may obtain a copy of the License at
-- 
--        http://www.apache.org/licenses/LICENSE-2.0
-- 
-- Unless required by applicable law or agreed to in writing, software
-- distributed under the License is distributed on an "AS IS" BASIS,
-- WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
-- See the License for the specific language governing permissions and
-- limitations under the License.
-- 
-- @author Matthew Brennan
-- @author Miklos Pataky
-- @author Lwin Moe
-- @author Keith Buel
-- @copyright 2008 eGloo, LLC
-- @license http://www.apache.org/licenses/LICENSE-2.0
-- @package Database
-- @version 1.0


-- For Prefixes and Suffixes I will need to differentiate between different languages.  Can be fixed up later.
-- Also need professional prefixes and suffixes compared to other forms of prefix and suffix.
CREATE TABLE NameSuffixes (
	NameSuffix															VARCHAR(10) NOT NULL, -- Resize?
CONSTRAINT pk_NameSuffixes PRIMARY KEY (NameSuffix)
);

CREATE TABLE ProfessionalSuffixes (
	ProfessionalSuffix													VARCHAR(4) NOT NULL,
CONSTRAINT pk_ProfessionalSuffixes PRIMARY KEY (ProfessionalSuffix)
);

CREATE TABLE ProfessionalPrefixes (
	ProfessionalPrefix													VARCHAR(10) NOT NULL,
CONSTRAINT pk_ProfessionalPrefixes PRIMARY KEY (ProfessionalPrefix)
);

CREATE TABLE NamePrefixes (
	NamePrefix															VARCHAR(10) NOT NULL, -- Resize?
CONSTRAINT pk_NamePrefixes PRIMARY KEY (NamePrefix)
);

-- Might want to change this to sex.
CREATE TABLE Genders (
	Gender																VARCHAR(10) NOT NULL,
CONSTRAINT pk_Genders PRIMARY KEY (Gender)
);

CREATE TABLE UserTypes (
	UserType															VARCHAR(15) NOT NULL,
CONSTRAINT pk_UserTypes PRIMARY KEY (UserType)
);

--Display name will allow caps and spaces, to be dealt with at another time.
CREATE TABLE PageNames (
	PageName															VARCHAR(35) NOT NULL, -- 3 characters long, no spaces, alphanumerics only. (lower case) (WRAP INSERTS)
--	DisplayName															VARCHAR(50) NOT NULL, -- Only allow addition of spaces to pagename, and capitals.
CONSTRAINT pk_PageNames PRIMARY KEY (PageName)
);

CREATE SEQUENCE seq_Users_User_ID
    INCREMENT 		1
    MINVALUE 		-9223372036854775808
    MAXVALUE 		9223372036854775807;

-- Turn certian entries for suffixes and prefixes into null.
CREATE TABLE Users (
    User_ID																BIGINT DEFAULT NEXTVAL('seq_Users_User_ID') NOT NULL, -- This is here for security with cookies.
    UserType															VARCHAR(15) NOT NULL,
    UserName															VARCHAR(35) NOT NULL,
    UserPasswordHash													CHAR(64) NOT NULL, -- AlphaNumeric + 64 characters long.
    PassPhraseQuestion													VARCHAR(100) NOT NULL, -- Check for specific characters tha are allowed?
    PassPhraseAnswer													VARCHAR(64) NOT NULL, -- AlphaNumeric + 64 characters long.
    UserRegistrationDate												TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
   	BirthDate															DATE NOT NULL, -- Check birthdate, over 13
	ProfessionalPrefix													VARCHAR(10),
	NamePrefix															VARCHAR(10),
    FirstName															VARCHAR(35) NOT NULL, -- Lower + Uppercase + spaces + apostrophes.
    MiddleName															VARCHAR(35), -- Lower + Uppercase + spaces + apostrophes.
    LastName															VARCHAR(35) NOT NULL, -- Lower + Uppercase + spaces + apostrophes.
    NameSuffix															VARCHAR(10),
    ProfessionalSuffix													VARCHAR(4),
    Gender																VARCHAR(10) NOT NULL, 
    NumberOfInvites														SMALLINT NOT NULL DEFAULT 0,
	UserAssociationLevel												SMALLINT NOT NULL, -- Need different name
	Active																BOOLEAN DEFAULT FALSE,			
CONSTRAINT pk_Users PRIMARY KEY (User_ID),
CONSTRAINT fk_Users_UserName FOREIGN KEY (UserName)
	REFERENCES PageNames(PageName)
	MATCH FULL
	ON DELETE NO ACTION
	ON UPDATE CASCADE,
CONSTRAINT fk_Users_ProfessionalPrefix FOREIGN KEY (ProfessionalPrefix)
	REFERENCES ProfessionalPrefixes(ProfessionalPrefix)
	MATCH FULL
	ON DELETE NO ACTION
	ON UPDATE CASCADE,
CONSTRAINT fk_Users_NamePrefix FOREIGN KEY (NamePrefix)
	REFERENCES NamePrefixes(NamePrefix)
	MATCH FULL
	ON DELETE NO ACTION
	ON UPDATE CASCADE,
CONSTRAINT fk_Users_NameSuffix FOREIGN KEY (NameSuffix)
	REFERENCES NameSuffixes(NameSuffix)
	MATCH FULL
	ON DELETE NO ACTION
	ON UPDATE CASCADE,
CONSTRAINT fk_Users_ProfesionalSuffix FOREIGN KEY (ProfessionalSuffix)
	REFERENCES ProfessionalSuffixes(ProfessionalSuffix)
	MATCH FULL
	ON DELETE NO ACTION
	ON UPDATE CASCADE,
CONSTRAINT fk_Users_Gender FOREIGN KEY (Gender)
	REFERENCES Genders(Gender)
	MATCH FULL
	ON DELETE NO ACTION
	ON UPDATE CASCADE--,
--CONSTRAINT ck_Users_UserPasswordHash_CharacterTypes CHECK (UserPasswordHash ~* [a-z0-9])
--CONSTRAINT ck_Users_BirthDate CHECK (AGE(CURRENT_TIMESTAMP,BirthDate) >= INTERVAL '13 years')
);

-- Next point
CREATE TABLE EmailAddresses (
	EmailAddress														VARCHAR(320) NOT NULL, --Check for specific characters. (Read about dealing with periods) Check for format characters@characters.characters)
	EmailAddress_DateAdded												TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	-- User that added address? Probably a good idea to have.
	-- Whether it was added in creation or as a reference?
CONSTRAINT pk_Email PRIMARY KEY (EmailAddress)
);

-- Double check the logic behind the email verification.
-- Need a way for only one main email address, this will probably involve a change to table structure.
-- Its a normalization issue. Create table of main user email addresss.
CREATE TABLE UserEmailAddresses (
	User_ID																BIGINT NOT NULL,
	EmailAddress														VARCHAR(320) NOT NULL,
	EmailVerified														BOOLEAN DEFAULT FALSE NOT NULL ,
	DateEmailVerified													TIMESTAMP,  -- need a trigger to update this when an email address is verified.
	UserMainEmailAddress		 										BOOLEAN DEFAULT FALSE NOT NULL, -- need to make sure that users have only one main email address.
CONSTRAINT pk_UserEmailAddresses PRIMARY KEY (User_ID,EmailAddress),
CONSTRAINT fk_UserEmailAddresses_User_ID FOREIGN KEY (User_ID)
	REFERENCES Users(User_ID)
	MATCH FULL
	ON DELETE CASCADE -- Want the user's email deleted.
	ON UPDATE CASCADE,
CONSTRAINT fk_UserEmailAddresses_EmailAddress FOREIGN KEY (EmailAddress)
	REFERENCES EmailAddresses(EmailAddress)
	MATCH FULL
	ON DELETE CASCADE -- if the email is deleted delete the user's email.
	ON UPDATE CASCADE
);
-- Need to make a trigger on this table to make verification stuff.

--Not being used right now . . .
CREATE TABLE VerifyEmailAddress (
	EmailAddress														BIGINT NOT NULL,
	User_ID																BIGINT NOT NULL,
	EmailVerificationHex												VARCHAR(10) NOT NULL UNIQUE, -- might need to be longer
	EmailVerificationTimeStamp											TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	EmailVerificationValidTill											TIMESTAMP NOT NULL DEFAULT (CURRENT_TIMESTAMP + INTERVAL '3 hours'), -- Might want to use a trigger for this. . . to get proper timestamp.
CONSTRAINT pk_VerifyEmailAddress PRIMARY KEY (EmailAddress,User_ID,EmailVerificationHex),
CONSTRAINT fk_VerifyEmailAddress_EmailAddress FOREIGN KEY (EmailAddress)
	REFERENCES EmailAddresses(EmailAddress)
	MATCH FULL
	ON DELETE NO ACTION
	ON UPDATE CASCADE,
CONSTRAINT fk_VerifyEmailAddress_User_ID FOREIGN KEY (User_ID)
	REFERENCES Users(User_ID)
	MATCH FULL
	ON DELETE NO ACTION
	ON UPDATE CASCADE
);

-- Next point
-- Might want to have this in user?
-- Do all users have an association level?
CREATE TABLE UserInvitations (
	User_ID																BIGINT NOT NULL,
	EmailAddress														VARCHAR(320) NOT NULL, -- Email check
	Referral_ID															VARCHAR(20) NOT NULL, -- Change type after talking with Mix Master AlphaNumerics 20 characters long
	DateInvited															TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	InvitationAccepted													BOOLEAN NOT NULL DEFAULT FALSE,
	InvitedUser_ID                                                      BIGINT, 
	Confirmation_ID                                                     VARCHAR(20),
CONSTRAINT pk_UserInvites PRIMARY KEY (Referral_ID),
CONSTRAINT fk_UserInvitations_User_ID FOREIGN KEY (User_ID)
	REFERENCES Users(User_ID)
	MATCH FULL
	ON DELETE NO ACTION
	ON UPDATE CASCADE,
CONSTRAINT fk_UserInvitations_InvitedUser_ID FOREIGN KEY (User_ID)
	REFERENCES Users(User_ID)
	MATCH FULL
	ON DELETE NO ACTION
	ON UPDATE CASCADE
);
-- Trugger so that when an invitation is made the numbers get reduced	
-- Make sure an email address used is not a main email address for a user
-- Should inivitations expire at all?

CREATE TABLE UserReferences (
	Referral_ID															VARCHAR(20) NOT NULL, -- User that did the inviting.
	InvitedUser_ID														BIGINT NOT NULL, -- User that accepted the invite.
CONSTRAINT pk_UserReferences PRIMARY KEY (Referral_ID,InvitedUser_ID),
CONSTRAINT fk_UserReferences_User_ID FOREIGN KEY (Referral_ID)
	REFERENCES UserInvitations(Referral_ID)
	MATCH FULL
	ON DELETE NO ACTION
	ON UPDATE CASCADE,
CONSTRAINT fk_UserReferences_InvitedUser_ID FOREIGN KEY (InvitedUser_ID)
	REFERENCES Users(User_ID)
	MATCH FULL
	ON DELETE NO ACTION
	ON UPDATE CASCADE
);
-- Need to ask Mix Master about users that could be invited.
-- Stuff for developers and so forth . . .

--Next point
CREATE TABLE Countries (
	Alpha_2ISOCode														VARCHAR(2) NOT NULL,
	Alpha_3ISOCode														VARCHAR(3) NOT NULL,
	NumericISOCode														VARCHAR(3) NOT NULL, -- INT of some sort?
	CountryName															VARCHAR(45) NOT NULL,
CONSTRAINT pk_Countries PRIMARY KEY (Alpha_2ISOCode)
);

CREATE SEQUENCE seq_Addresses_Address_ID
    INCREMENT 		1
    MINVALUE 		-9223372036854775808
    MAXVALUE 		9223372036854775807;

CREATE TABLE Addresses (
	Address_ID															BIGINT DEFAULT NEXTVAL('seq_Addresses_Address_ID') NOT NULL,
	CountryISO															VARCHAR(2) NOT NULL,
	DateAddressCreated													TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	AddressCreator														BIGINT NOT NULL,
CONSTRAINT pk_Address PRIMARY KEY (Address_ID),
CONSTRAINT fk_Address_CountryISO FOREIGN KEY (CountryISO)
	REFERENCES Countries(Alpha_2ISOCode)
	MATCH FULL
	ON DELETE NO ACTION
	ON UPDATE CASCADE,
CONSTRAINT fk_Address_AddressCreator FOREIGN KEY (AddressCreator)
	REFERENCES Users(User_ID)
	MATCH FULL
	ON DELETE NO ACTION
	ON UPDATE CASCADE
);

CREATE TABLE US_StateProvinces (
	US_StateProvince_ID													VARCHAR(2) NOT NULL,
	US_StateProvinceName												VARCHAR(22) NOT NULL,
CONSTRAINT pk_US_StateProvince PRIMARY KEY (US_StateProvince_ID)
);

-- Need normalization for zip?
CREATE TABLE US_Addresses (
	US_Address_ID														BIGINT NOT NULL,
	US_AddressLine1														VARCHAR(100) NOT NULL, -- Disallow specific characters.
	US_AddressLine2														VARCHAR(100), -- Disallow specific characters
	US_CityTown															VARCHAR(50) NOT NULL,
	US_StateProvince_ID													VARCHAR(2) NOT NULL,
	US_PostalCode1														VARCHAR(5) NOT NULL, -- Numeric 5 digits
	US_PostalCode2														VARCHAR(4), -- Numeric 4 digits
CONSTRAINT pk_US_Addresses PRIMARY KEY (US_Address_ID),
CONSTRAINT fk_US_Addresses_Address_ID FOREIGN KEY (US_Address_ID)
	REFERENCES Addresses(Address_ID)
	MATCH FULL
	ON DELETE NO ACTION
	ON UPDATE CASCADE,
CONSTRAINT fk_US_StateProvinces_US_StateProvince_ID FOREIGN KEY (US_StateProvince_ID)
	REFERENCES US_StateProvinces(US_StateProvince_ID)
	MATCH FULL
	ON DELETE NO ACTION
	ON UPDATE CASCADE
);

--Legal address needs another table to make sure its mutually exlcusive
CREATE TABLE UserAddresses (
	User_ID																BIGINT NOT NULL,
	Address_ID															BIGINT NOT NULL,
	LegalAddress														BOOLEAN DEFAULT FALSE NOT NULL,
	UserAddressLabel													VARCHAR(20) NOT NULL DEFAULT 'Home',
CONSTRAINT pk_UserAddresses PRIMARY KEY (User_ID,Address_ID),
CONSTRAINT fk_UserAddresses_User_ID FOREIGN KEY (User_ID)
	REFERENCES Users(User_ID)
	MATCH FULL
	ON DELETE NO ACTION
	ON UPDATE CASCADE,
CONSTRAINT fk_ProfileAddresses_Address_ID FOREIGN KEY (Address_ID)
	REFERENCES Addresses(Address_ID)
	MATCH FULL
	ON DELETE NO ACTION
	ON UPDATE CASCADE
);
-- Need address verification
-- Would be a leter printed and sent.

-- Next point
CREATE SEQUENCE seq_LoginAttempts_LoginAttempt_ID
    INCREMENT 		1
    MINVALUE 		-9223372036854775808
    MAXVALUE 		9223372036854775807;

CREATE TABLE LoginAttempts (
	LoginAttempt_ID														BIGINT DEFAULT NEXTVAL('seq_LoginAttempts_LoginAttempt_ID') NOT NULL,
	UserName															VARCHAR(35) NOT NULL,
	LoginTime															TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
	IPAddress															INET NOT NULL,
	UserAgent															VARCHAR(100) NOT NULL, -- check varchar size,
	Successful															BOOLEAN NOT NULL,
CONSTRAINT pk_Logins PRIMARY KEY (LoginAttempt_ID)
-- Foriegn key username?
);

CREATE TABLE SuccessfulLogins (
	LoginAttempt_ID														BIGINT NOT NULL,
	User_ID																BIGINT NOT NULL,
CONSTRAINT pk_SuccessfulLogins PRIMARY KEY (LoginAttempt_ID),
CONSTRAINT fk_SuccessfulLogins FOREIGN KEY (LoginAttempt_ID)
	REFERENCES LoginAttempts(LoginAttempt_ID)
	MATCH FULL
	ON DELETE NO ACTION
	ON UPDATE CASCADE
);

CREATE TABLE UnsuccessfulLogins (
	LoginAttempt_ID														BIGINT NOT NULL,
	PasswordHash														CHAR(64) NOT NULL,
CONSTRAINT pk_UnsuccessfulLogins PRIMARY KEY (LoginAttempt_ID),
CONSTRAINT fk_UnsuccessfulLogins_LoginAttempt_ID FOREIGN KEY (LoginAttempt_ID)
	REFERENCES LoginAttempts(LoginAttempt_ID)
	MATCH FULL
	ON DELETE NO ACTION
	ON UPDATE CASCADE
);

CREATE TABLE Sessions (
	Session_ID															VARCHAR(32) NOT NULL, -- Alpha numeric 32 chars long.
	DateAccessed														TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
	UserAgent															VARCHAR(100) NOT NULL, -- Will there be a table of user agents? NO! *TEAR*
	SessionData															TEXT NOT NULL,  -- Need more info about how sessions work before changing this.
CONSTRAINT pk_Sessions_Session_ID PRIMARY KEY (Session_ID)
);

CREATE TABLE IdentifiedSessions (
	Session_ID															VARCHAR(32) NOT NULL,
	User_ID																BIGINT NOT NULL,
CONSTRAINT pk_IdentifiedSessions PRIMARY KEY (Session_ID),
CONSTRAINT fk_IdentifiedSessions_Session_ID FOREIGN KEY (Session_ID)
	REFERENCES Sessions(Session_ID)
	MATCH FULL
	ON DELETE CASCADE -- this cascades so that don't need to modify deleteOldSessoins function
	ON UPDATE CASCADE,
CONSTRAINT fk_IdentifiedSessions_User_ID FOREIGN KEY (User_ID)
	REFERENCES Users(User_ID)
	MATCH FULL
	ON DELETE NO ACTION
	ON UPDATE CASCADE
);	
-- add last accessed timestamp, default at creation.
-- Possible access table if needed for security.
-- mabye create date created feield?
-- Need function to create sessions
-- Need function to read + update sessions.

CREATE SEQUENCE seq_Profiles_Profile_ID
    INCREMENT 		1
    MINVALUE 		-9223372036854775808
    MAXVALUE 		9223372036854775807;

-- Cardnality (group or individually owned)
CREATE TABLE ProfileTypes (
	ProfileType															VARCHAR(14) NOT NULL,
CONSTRAINT pk_ProfileTypes PRIMARY KEY (ProfileType)
);

-- Need locations to determine what is adult or not.
CREATE TABLE Profiles (
	Profile_ID															BIGINT DEFAULT NEXTVAL('seq_Profiles_Profile_ID') NOT NULL,
	ProfileCreator														BIGINT NOT NULL,
	ProfileCreationDate													TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
	ProfileName															VARCHAR(35) NOT NULL, -- Might be a little redundant. Display name from pagenames?
	ProfilePageLayout													TEXT, --Default to an empty string -- Not null for now . . . at later point make not null and insert at profile creation.
	-- Profile preferences go here?
	-- List em off . . .
	--  - 
	AdultProfile														BOOLEAN DEFAULT TRUE NOT NULL, -- Need two triggers to update underage upon creation and to check on each login?
	ProfileType															VARCHAR(14) NOT NULL,
	-- Profile stuff that isn't always needed.
	
CONSTRAINT pk_Profiles PRIMARY KEY (Profile_ID),
CONSTRAINT fk_Profiles_ProfileCreator FOREIGN KEY (ProfileCreator)
	REFERENCES Users(User_ID)
	MATCH FULL
	ON DELETE NO ACTION
	ON UPDATE CASCADE,
CONSTRAINT fk_Profiles_ProfileName FOREIGN KEY (ProfileName) -- might want to change to display names.
	REFERENCES PageNames(PageName)
	MATCH FULL
	ON DELETE NO ACTION
	ON UPDATE CASCADE,
CONSTRAINT fk_Profiles_ProfileType FOREIGN KEY (ProfileType)
	REFERENCES ProfileTypes(ProfileType)
	MATCH FULL
	ON DELETE NO ACTION
	ON UPDATE CASCADE
);
-- Default settings for cubes somewhere here

-- Need locations to determine what is underage and not.
-- Options that owners of pages can set.
-- Need to check what age the creator/owners of pages are.
-- If a page is an adult page it cannot be passed off to an underage person.
-- Need to talk to a lawyer about location stuff, and about implecations of different ages.
-- So if 16 is the age one place, and 18 another, we need to make sure that model ages are well proper based on the place.
-- Need to find out whether it matters where the stuff is stored.
CREATE TABLE AdultProfiles (
	Profile_ID															BIGINT NOT NULL,
	NudityVisableToOwner												BOOLEAN NOT NULL DEFAULT FALSE,
	ProfanityVisableToOwner												BOOLEAN NOT NULL DEFAULT FALSE,
	NudityVisibleToViewers												BOOLEAN NOT NULL DEFAULT FALSE,
	ProfanityVisibleToViewers											BOOLEAN NOT NULL DEFAULT FALSE,
CONSTRAINT pk_AdultProfiles PRIMARY KEY (Profile_ID),
CONSTRAINT fk_AdultProfiles_Profile_ID FOREIGN KEY (Profile_ID)
	REFERENCES Profiles(Profile_ID)
	MATCH FULL
	ON DELETE NO ACTION
	ON UPDATE CASCADE
);

-- UserNames need to checked against profile names and group names.
	
-- Onwer for individual pages because someone may wish to hand off a page, especially troll or character pages .
CREATE TABLE IndividualProfiles (
	Profile_ID															BIGINT NOT NULL,
	MainProfile															BOOLEAN NOT NULL,
	-- Individual page specific preferences go here.
CONSTRAINT pk_IndividualProfiles PRIMARY KEY (Profile_ID),
CONSTRAINT fk_IndividualProfiles_Profile_ID FOREIGN KEY (Profile_ID)
	REFERENCES Profiles(Profile_ID)
	MATCH FULL
	ON DELETE NO ACTION
	ON UPDATE CASCADE
);

--Next point
CREATE TABLE LookingForOptions (
	LookingForOption													VARCHAR(20) NOT NULL,
CONSTRAINT pk_LookingForOptions PRIMARY KEY (LookingForOption)
);

-- Stuff like friendship or whatever.
CREATE TABLE LookingFor (
	Profile_ID															BIGINT NOT NULL, --Individual ones
	LookingForOption													VARCHAR(20) NOT NULL,
CONSTRAINT pk_LookingFor PRIMARY KEY (Profile_ID,LookingForOption),
CONSTRAINT fk_LookingFor_Profile_ID FOREIGN KEY (Profile_ID)
	REFERENCES IndividualProfiles(Profile_ID)
	MATCH FULL
	ON DELETE NO ACTION
	ON UPDATE CASCADE,
CONSTRAINT fk_LookingFor_LookingForOption FOREIGN KEY (LookingForOption)
	REFERENCES LookingForOptions(LookingForOption)
	MATCH FULL
	ON DELETE NO ACTION
	ON UPDATE CASCADE
);

-- Interested in.
-- Interested in . . . part of the profile
-- Change gender in users table to sex mabye
-- Look up definition of gender and sex . . .
-- Might as well go by the common terms.
-- Ask Amy she would know.
CREATE TABLE SexualPreferenceOptions (
	Gender																VARCHAR(20) NOT NULL, -- Figure this out after population
CONSTRAINT pk_SexualPreferenceOptions PRIMARY KEY (Gender)
);
	
CREATE TABLE SexualPreferences (
	Profile_ID															BIGINT NOT NULL,
	Gender																VARCHAR(20) NOT NULL,
CONSTRAINT pk_SexualPreferences PRIMARY KEY (Profile_ID,Gender),
CONSTRAINT fk_SexualPreferences_Profile_ID FOREIGN KEY (Profile_ID)
	REFERENCES IndividualProfiles(Profile_ID)
	MATCH FULL
	ON DELETE NO ACTION
	ON UPDATE CASCADE,
CONSTRAINT fk_SexualPreferences_Gender FOREIGN KEY (Gender)
	REFERENCES SexualPreferenceOptions(Gender)
	MATCH FULL
	ON DELETE NO ACTION
	ON UPDATE CASCADE
);

-- Should this be part of the profile table?
CREATE TABLE HomeTowns (
	Profile_ID															BIGINT NOT NULL, -- Any profile.
	CountryISOCode														VARCHAR(2) NOT NULL,
CONSTRAINT pk_HomeTowns PRIMARY KEY (Profile_ID),
CONSTRAINT fk_HomeTowns_Profile_ID FOREIGN KEY (Profile_ID)
	REFERENCES Profiles(Profile_ID)
	MATCH FULL
	ON DELETE NO ACTION
	ON UPDATE CASCADE,
CONSTRAINT fk_HomeTowns_CountryISOCode FOREIGN KEY (CountryISOCode)
	REFERENCES Countries(Alpha_2ISOCode)
	MATCH FULL
	ON DELETE NO ACTION
	ON UPDATE CASCADE
);

CREATE TABLE US_HomeTowns (
	Profile_ID															BIGINT NOT NULL,
	US_CityTown															VARCHAR(50) NOT NULL,
	US_StateProvince_ID													VARCHAR(2) NOT NULL,
CONSTRAINT pk_US_HomeTowns PRIMARY KEY (Profile_ID),
CONSTRAINT fk_US_HomeTowns_Profile_ID FOREIGN KEY (Profile_ID)
	REFERENCES Profiles(Profile_ID)
	MATCH FULL
	ON DELETE NO ACTION
	ON UPDATE CASCADE,
CONSTRAINT fk_US_StateProvince_ID FOREIGN KEY (US_StateProvince_ID)
	REFERENCES US_StateProvinces(US_StateProvince_ID)
	MATCH FULL
	ON DELETE NO ACTION
	ON UPDATE CASCADE
);

CREATE SEQUENCE seq_Relationshps_Relationship_ID
    INCREMENT 		1
    MINVALUE 		-9223372036854775808
    MAXVALUE 		9223372036854775807;

CREATE TABLE Relationships (
	Relationship_ID														BIGINT DEFAULT NEXTVAL('seq_Relationshps_Relationship_ID') NOT NULL,
	BiDirectionalRelationship											BOOLEAN NOT NULL,
	Accepted 															BOOLEAN NOT NULL,
	AccepterProfile_ID													BIGINT NOT NULL,
	RequesterProfile_ID													BIGINT NOT NULL,
CONSTRAINT pk_Relationships PRIMARY KEY (Relationship_ID),
CONSTRAINT fk_Relationships_AccepterProfile_ID FOREIGN KEY (AccepterProfile_ID)
	REFERENCES Profiles(Profile_ID)
	MATCH FULL
	ON UPDATE CASCADE
	ON DELETE NO ACTION,
CONSTRAINT fk_Relationships_RequesterProfile_ID FOREIGN KEY (RequesterProfile_ID)
	REFERENCES Profiles(Profile_ID)
	MATCH FULL
	ON UPDATE CASCADE
	ON DELETE NO ACTION
);

-- Table: bidirectionalrelationshiptypes
CREATE TABLE BiDirectionalRelationshipTypes (
	RelationshipType													VARCHAR(50) NOT NULL,
CONSTRAINT pk_BiDirectionalRelationshipTypes PRIMARY KEY (RelationshipType)
); 
--WITHOUT OIDS;
--ALTER TABLE bidirectionalrelationshiptypes OWNER TO keith;

-- Table: bidirectionalrelationship
CREATE TABLE BiDirectionalRelationships (
	Relationship_ID														BIGINT NOT NULL,
	Profile_ID1															BIGINT NOT NULL,
	Profile_ID2															BIGINT NOT NULL,
	RelationshipType 													VARCHAR(50) NOT NULL,
CONSTRAINT pk_BidirectionalRelationships PRIMARY KEY (Relationship_ID),
CONSTRAINT fk_BiDirectionalRelationships_Relationship_ID FOREIGN KEY (Relationship_ID)
	REFERENCES Relationships(Relationship_ID)
	MATCH FULL
	ON UPDATE CASCADE 
	ON DELETE CASCADE,
CONSTRAINT fk_BiDirectionalRelationships_ProfileID1 FOREIGN KEY (Profile_ID1)
	REFERENCES Profiles(Profile_ID)
	MATCH FULL
	ON UPDATE NO ACTION 
	ON DELETE NO ACTION,
CONSTRAINT fk_bidirectionalrelationship_profileID2 FOREIGN KEY (Profile_ID2)
	REFERENCES Profiles(Profile_ID) 
	MATCH FULL
	ON UPDATE NO ACTION 
	ON DELETE NO ACTION,
CONSTRAINT fk_bidirectionalrelationshiptype FOREIGN KEY (RelationshipType)
	REFERENCES BiDirectionalRelationshiptypes(RelationshipType) 
	MATCH FULL
	ON UPDATE NO ACTION
	ON DELETE NO ACTION
);
--WITHOUT OIDS;
--ALTER TABLE bidirectionalrelationship OWNER TO keith;

-- Table: unidirectionalrelationshiptypes
CREATE TABLE UniDirectionalRelationshipTypes (
	RelationshipLabel													VARCHAR(100) NOT NULL,	
	ParentName															VARCHAR(50) NOT NULL,
	ChildName															VARCHAR(50) NOT NULL,
CONSTRAINT pk_unidirectionalrelationshiptypes PRIMARY KEY(RelationshipLabel)
);
--WITHOUT OIDS;
--ALTER TABLE unidirectionalrelationshiptypes OWNER TO keith;

-- Table: unidirectionalrelationship
CREATE TABLE UniDirectionalRelationships (
	Relationship_ID														BIGINT NOT NULL,
	ParentProfile_ID													BIGINT NOT NULL,
	ChildProfile_ID														BIGINT NOT NULL,
	RelationshipLabel													VARCHAR(100) NOT NULL,
CONSTRAINT pk_UniDirectionalRelationship PRIMARY KEY (ParentProfile_ID,ChildProfile_ID,RelationshipLabel),
CONSTRAINT fk_UniDirectionalRelationship_Relationship_ID FOREIGN KEY (Relationship_ID)
	REFERENCES Relationships(Relationship_ID)
	MATCH FULL
	ON UPDATE CASCADE 
	ON DELETE CASCADE,
CONSTRAINT fk_UniDirectionalRelationship_ChildProfileID FOREIGN KEY (ChildProfile_ID)
	REFERENCES profiles (profile_id) MATCH SIMPLE
	ON UPDATE NO ACTION ON DELETE NO ACTION,
CONSTRAINT fk_UniDirectionalRelationship_ParentProfile_ID FOREIGN KEY (ParentProfile_ID)
	REFERENCES Profiles(Profile_ID) 
	MATCH FULL
	ON UPDATE NO ACTION 
	ON DELETE NO ACTION,
CONSTRAINT fk_UniDirectionalRelationshipLabel FOREIGN KEY (RelationshipLabel)
	REFERENCES UniDirectionalRelationshipTypes(RelationshipLabel) 
	MATCH FULL
	ON UPDATE NO ACTION 
	ON DELETE NO ACTION
);
--WITHOUT OIDS;
--ALTER TABLE unidirectionalrelationship OWNER TO keith;


CREATE TABLE ElementPackages (
	ElementPackage														VARCHAR(50) NOT NULL,
CONSTRAINT pk_ElementPackages PRIMARY KEY (ElementPackage)
);

CREATE SEQUENCE seq_ElementTypes_ElementType_ID
    INCREMENT 		1
    MINVALUE 		-9223372036854775808
    MAXVALUE 		9223372036854775807;

-- Need to designate types that are limited somewhere
CREATE TABLE ElementTypes (
	ElementType_ID														BIGINT DEFAULT NEXTVAL('seq_ElementTypes_ElementType_ID') NOT NULL,
	ElementType															VARCHAR(50) NOT NULL,  -- Label for the type.
	ElementPackagePath													TEXT, -- Need check for inproper path characters.
	ElementPackage														VARCHAR(50) NOT NULL, 
CONSTRAINT pk_ElmentTypes PRIMARY KEY (ElementType_ID),
CONSTRAINT fk_ElementTypes_ElementPackage FOREIGN KEY (ElementPackage)
	REFERENCES ElementPackages(ElementPackage)
	MATCH FULL
	ON DELETE NO ACTION
	ON UPDATE CASCADE
);

CREATE SEQUENCE seq_Elements_Element_ID
    INCREMENT 		1
    MINVALUE 		-9223372036854775808
    MAXVALUE 		9223372036854775807;

CREATE TABLE Elements (
	Element_ID															BIGINT DEFAULT NEXTVAL('seq_Elements_Element_ID') NOT NULL,
	ElementType_ID														BIGINT NOT NULL,
	Creator_ID															BIGINT NOT NULL, -- Profile that originally created the element.  Profiles own elements. Users own profiles.
	DateCreated															TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
CONSTRAINT pk_Elements PRIMARY KEY (Element_ID),
CONSTRAINT fk_Elements_ElementType_ID FOREIGN KEY (ElementType_ID)
	REFERENCES ElementTypes(ElementType_ID)
	MATCH FULL
	ON DELETE NO ACTION
	ON UPDATE CASCADE,
CONSTRAINT fk_Elements_Creator_ID FOREIGN KEY (Creator_ID)
	REFERENCES IndividualProfiles(Profile_ID)
	MATCH FULL
	ON DELETE NO ACTION
	ON UPDATE CASCADE
);

CREATE SEQUENCE seq_Blogs_Blog_ID
    INCREMENT 		1
    MINVALUE 		-9223372036854775808
    MAXVALUE 		9223372036854775807;

CREATE TABLE Blogs (
	Blog_ID																BIGINT DEFAULT NEXTVAL('seq_Blogs_Blog_ID') NOT NULL UNIQUE,
	BlogWriter															BIGINT NOT NULL,
	DateBlogCreated														TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
CONSTRAINT pk_Blogs PRIMARY KEY (Blog_ID),
CONSTRAINT fk_Blogs_BlogWriter FOREIGN KEY (BlogWriter)
	REFERENCES IndividualProfiles(Profile_ID)
	MATCH FULL
	ON DELETE NO ACTION
	ON UPDATE CASCADE
);		

CREATE TABLE BlogEntries (
	Blog_ID																BIGINT NOT NULL,
	DateEdited															TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
	BlogTitle															VARCHAR(100),
	BlogContent															TEXT NOT NULL,
CONSTRAINT pk_BlogsEntries PRIMARY KEY (Blog_ID,DateEdited),
CONSTRAINT fk_BlogsEntries_Blog_ID FOREIGN KEY (Blog_ID)
	REFERENCES Blogs(Blog_ID)
	MATCH FULL
	ON DELETE CASCADE -- IF a blog is deleted all of its modifications are too.
	ON UPDATE CASCADE
);

-- Files
CREATE TABLE MIMETypes (
	MIMEType															VARCHAR(100) NOT NULL, -- Check size
CONSTRAINT pk_MIMETypes PRIMARY KEY (MIMEType)
);

-- MIMEType is used as a check here to make sure a file uploaded with a file extension is associated with the proper mime type and vice versa.
-- It may be possible for a file extension to be associated with more than one subtype.
-- Double check about subtypes too especially with multipart mimetypes.
--CREATE TABLE FileExtensions (
--	FileExtension														VARCHAR(4) NOT NULL,
--	MIMEType															VARCHAR(100),
--CONSTRAINT pk_FileExtensions PRIMARY KEY (FileExtension),
--CONSTRAINT fk_FileExtensions_MIMEType FOREIGN KEY (MIMEType)
--	REFERENCES MIMETypes(MIMEType)
--	MATCH FULL
--	ON DELETE NO ACTION
--	ON UPDATE CASCADE
--);

-- Should there be a file_id?
-- Check uniqueness using hash plus mimetype or file extension or both.
CREATE TABLE Files (
	FileHash															VARCHAR(64) NOT NULL,
	MIMEType															VARCHAR(100) NOT NULL,
	File																TEXT NOT NULL, -- find out what base64 allows.
	FileSize															BIGINT NOT NULL, -- in bytes.
	DateUploaded														TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
	Uploader															BIGINT NOT NULL,
	FileName															TEXT NOT NULL, -- Maximum filename size.
	-- Need to learn about OS specific information: Date created, date last modified and so forth. permissions?
CONSTRAINT pk_Files PRIMARY KEY (FileHash, MIMEType), 
CONSTRAINT fk_Files_MIMEType FOREIGN KEY (MIMEType)
	REFERENCES MIMETypes(MIMEType)
	MATCH FULL
	ON DELETE NO ACTION
	ON UPDATE CASCADE,
CONSTRAINT fk_Files_Uploader FOREIGN KEY (Uploader)
	REFERENCES Users(User_ID)
	MATCH FULL
	ON DELETE NO ACTION
	ON UPDATE CASCADE
);

CREATE TABLE FileOwners (
	FileHash															VARCHAR(64) NOT NULL,
	MIMEType															VARCHAR(100) NOT NULL,
	User_ID																BIGINT NOT NULL,
CONSTRAINT pk_FileOwners PRIMARY KEY (FileHash, MIMEType, User_ID),
CONSTRAINT fk_FileOwners_FileHash_MIMEType FOREIGN KEY (FileHash,MIMEType)
	REFERENCES Files(FileHash,MIMEType)
	MATCH FULL
	ON DELETE CASCADE
	ON UPDATE CASCADE,
CONSTRAINT fk_FileOwners_User_ID FOREIGN KEY (User_ID)
	REFERENCES Users(User_ID)
	MATCH FULL
	ON DELETE CASCADE
	ON UPDATE CASCADE
);

-- Can normalize this more, into categories of different files
-- Involving images 

CREATE TABLE ImageFiles (
	FileHash															VARCHAR(64) NOT NULL,
	MIMEType															VARCHAR(100) NOT NULL,
	ImageDimensionX														SMALLINT NOT NULL,
	ImageDimensionY														SMALLINT NOT NULL,
CONSTRAINT pk_ImageFiles PRIMARY KEY (FileHash, MIMEType),
CONSTRAINT fk_ImageFiles_FileHash_MIMEType FOREIGN KEY (FileHash,MIMEType)
	REFERENCES Files(FileHash,MIMEType)
	MATCH FULL
	ON DELETE CASCADE
	ON UPDATE CASCADE
);	
	
CREATE TABLE Image_Elements (
	Element_ID															BIGINT NOT NULL,
	ImageFileHash														VARCHAR(64) NOT NULL,
	MIMEType															VARCHAR(100) NOT NULL,
	Title																VARCHAR(100),
	Summery																VARCHAR(4000),
CONSTRAINT pk_Image_Elements_Element_ID PRIMARY KEY (Element_ID),
CONSTRAINT fk_Image_Elements_Element_ID FOREIGN KEY (Element_ID)
	REFERENCES Elements(Element_ID)
	MATCH FULL
	ON DELETE CASCADE
	ON UPDATE NO ACTION,
CONSTRAINT fk_Image_Elements_ImageFileHash FOREIGN KEY (ImageFileHash,MIMEType)
	REFERENCES ImageFiles(FileHash,MIMEType)
	MATCH FULL
	ON DELETE CASCADE
	ON UPDATE CASCADE
);

-- Need to include profile in here somewhere. so that there is only one profile image element per profile.
CREATE TABLE ProfileImage_Elements (
	Element_ID															BIGINT NOT NULL,
	ImageFileHash														VARCHAR(64) NOT NULL,
	MIMEType															VARCHAR(100) NOT NULL,
CONSTRAINT pk_ProfileImage_Elements_Element_ID PRIMARY KEY (Element_ID),
CONSTRAINT fk_ProfileImage_Elements_Element_ID FOREIGN KEY (Element_ID)
	REFERENCES Elements(Element_ID)
	MATCH FULL
	ON DELETE CASCADE
	ON UPDATE NO ACTION,
CONSTRAINT fk_ProfileImage_Elements_ImageFileHash FOREIGN KEY (ImageFileHash,MIMEType)
	REFERENCES ImageFiles(FileHash,MIMEType)
	MATCH FULL
	ON DELETE CASCADE
	ON UPDATE CASCADE
);CREATE LANGUAGE 'plpgsql';

CREATE OR REPLACE FUNCTION setImageElement(input_User_ID BIGINT, input_Profile_ID BIGINT, input_ImageFileHash TEXT, input_MIMEType TEXT, input_Title TEXT, input_Summery TEXT, OUT output_Successful BOOLEAN, OUT output_Element_ID BIGINT, OUT output_ElementType_ID BIGINT, OUT output_ElementType TEXT, OUT output_ElementPackagePath TEXT) AS $setImageElement$
	DECLARE
	
	BEGIN
		PERFORM User_ID FROM FileOwners WHERE User_ID=input_User_ID AND FileHash=input_ImageFileHash AND MIMEType=input_MIMEType;
		IF FOUND THEN 
	
			INSERT INTO Elements (Creator_ID,ElementType_ID) VALUES (input_Profile_ID, (SELECT ElementType_ID FROM ElementTypes WHERE ElementType='ImageElement'));
			IF FOUND THEN
				INSERT INTO Image_Elements VALUES (currval('seq_Elements_Element_ID'), input_ImageFileHash, input_MIMEType, input_Title, input_Summery);
			END IF;
			
			output_Successful:=FOUND;
			
			SELECT INTO 
			output_Element_ID, output_ElementType_ID, output_ElementType, output_ElementPackagePath
			Elements.Element_ID, Elements.ElementType_ID, ElementTypes.ElementType, ElementTypes.ElementPackagePath
			FROM Elements INNER JOIN ElementTypes ON Elements.ElementType_ID=ElementTypes.ElementType_ID
			WHERE Elements.Element_ID=currval('seq_Elements_Element_ID');
		END IF;
	END;
$setImageElement$ LANGUAGE 'plpgsql';

CREATE OR REPLACE FUNCTION getProfileImageElement(input_Profile_ID BIGINT, OUT output_Element_ID BIGINT, OUT output_ElementType_ID BIGINT,  OUT output_ElementType TEXT, OUT output_ElementPackagePath TEXT, OUT output_Creator_ID BIGINT, OUT output_DateCreated TIMESTAMP, OUT output_ImageFileHash TEXT, OUT output_MIMEType TEXT, OUT output_File TEXT, OUT output_FileSize BIGINT, OUT output_DateUploaded TIMESTAMP, OUT output_Uploader BIGINT, OUT output_FileName TEXT, OUT output_ImageDimensionX SMALLINT, OUT output_ImageDimensionY SMALLINT) AS $getProfileImageElement$
	DECLARE
	
	BEGIN
		-- Need to make sure that planning out the entire list of joins is faster than just listing the tables.
		SELECT INTO
		output_Element_ID, output_ElementType_ID, output_ElementType, output_ElementPackagePath, output_Creator_ID, output_DateCreated, output_ImageFileHash, output_MIMEType, output_File, output_FileSize, output_DateUploaded, output_Uploader, output_FileName, output_ImageDimensionX, output_ImageDimensionY
		Elements.Element_ID, Elements.ElementType_ID, ElementTypes.ElementType, ElementTypes.ElementPackagePath, Elements.Creator_ID, Elements.DateCreated, ProfileImage_Elements.ImageFileHash, ProfileImage_Elements.MIMEType, Files.File,	Files.FileSize,	Files.DateUploaded,	Files.Uploader, Files.FileName, ImageFiles.ImageDimensionX, ImageFiles.ImageDimensionY
		FROM ElementTypes INNER JOIN Elements ON ElementTypes.ElementType_ID=Elements.ElementType_ID 
			INNER JOIN ProfileImage_Elements ON Elements.Element_ID=ProfileImage_Elements.Element_ID 
			INNER JOIN ImageFiles ON ProfileImage_Elements.ImageFileHash=ImageFiles.FileHash AND ProfileImage_Elements.MIMEType=ImageFiles.MIMEType 
			INNER JOIN Files ON ImageFiles.FileHash=Files.FileHash AND ImageFiles.MIMEType=Files.MIMEType
		WHERE Elements.Creator_ID=input_Profile_ID;
		
	END;
$getProfileImageElement$ LANGUAGE 'plpgsql';

CREATE OR REPLACE FUNCTION removeElement(input_Profile_ID BIGINT, input_Element_ID BIGINT, OUT output_Successful BOOLEAN) AS $removeElement$
	DECLARE
		
	BEGIN
		DELETE FROM Elements WHERE Element_ID=internal_Element_ID AND Creator_ID=input_Profile_ID;
		output_Successful:=FOUND;
		
	END;
$removeElement$ LANGUAGE 'plpgsql';

CREATE OR REPLACE FUNCTION getImageElementInstance(input_Element_ID BIGINT, OUT output_ElementType TEXT, OUT output_ElementPackagePath TEXT, OUT output_Creator_ID BIGINT, OUT output_DateCreated TIMESTAMP, OUT output_ImageFileHash TEXT, OUT output_MIMEType TEXT, OUT output_Title TEXT, OUT output_Summery TEXT, OUT output_File TEXT, OUT output_FileSize BIGINT, OUT output_DateUploaded TIMESTAMP, OUT output_Uploader BIGINT, OUT output_FileName TEXT, OUT output_ImageDimensionX SMALLINT, OUT output_ImageDimensionY SMALLINT) AS $getImageElementInstance$
	DECLARE
	
	BEGIN
		-- Need to make sure that planning out the entire list of joins is faster than just listing the tables.
		SELECT INTO
		output_ElementType, output_ElementPackagePath, output_Creator_ID, output_DateCreated, output_ImageFileHash, output_MIMEType, output_Title, output_Summery, output_File, output_FileSize, output_DateUploaded, output_Uploader, output_FileName, output_ImageDimensionX, output_ImageDimensionY
		ElementTypes.ElementType, ElementTypes.ElementPackagePath, Elements.Creator_ID, Elements.DateCreated, Image_Elements.ImageFileHash, Image_Elements.MIMEType, Image_Elements.Title, Image_Elements.Summery, Files.File,	Files.FileSize,	Files.DateUploaded,	Files.Uploader, ImageFiles.ImageDimensionX, ImageFiles.ImageDimensionY
		FROM ElementTypes INNER JOIN Elements ON ElementTypes.ElementType_ID=Elements.ElementType_ID 
			INNER JOIN Image_Elements ON Elements.Element_ID=Image_Elements.Element_ID 
			INNER JOIN ImageFiles ON Image_Elements.ImageFileHash=ImageFiles.FileHash AND Image_Elements.MIMEType=ImageFiles.MIMEType 
			INNER JOIN Files ON ImageFiles.FileHash=Files.FileHash AND ImageFiles.MIMEType=Files.MIMEType
		WHERE ImageElements.Element_ID=input_Element_ID;
		
	END;
$getImageElementInstance$ LANGUAGE 'plpgsql';

CREATE OR REPLACE FUNCTION setProfileImage(input_User_ID BIGINT, input_Profile_ID BIGINT, input_ImageFileHash TEXT, input_MIMEType TEXT, OUT output_Successful BOOLEAN, OUT output_Element_ID BIGINT, OUT output_ElementType_ID BIGINT, OUT output_ElementType TEXT, OUT output_ElementPackagePath TEXT) AS $setProfileImage$
	DECLARE
		internal_Element_ID BIGINT;
	BEGIN
		-- Need to make sure User has ownership of the image!
		-- if ProfileImage_Element exists then run the removeProfileImage function
		SELECT INTO
		internal_Element_ID 
		ProfileImage_Elements.Element_ID 
		FROM ProfileImage_Elements  INNER JOIN Elements ON ProfileImage_Elements.Element_ID=Elements.Element_ID INNER JOIN Profiles ON Elements.Creator_ID=Profiles.Profile_ID
		WHERE Profiles.Profile_ID=input_Profile_ID AND Profiles.ProfileCreator=input_User_ID;
		
		IF FOUND THEN
			DELETE FROM ProfileImage_Elements WHERE Element_ID=internal_Element_ID;
		END IF;
		
		PERFORM ProfileCreator FROM Profiles WHERE Profile_ID=input_Profile_ID AND ProfileCreator=input_User_ID;
		
		IF FOUND THEN
			PERFORM User_ID FROM FileOwners WHERE FileHash=input_ImageFileHash AND MIMEType=input_MIMEType AND User_ID=input_User_ID;
				-- then run the createNewElement for ProfileImage_Elements
			IF FOUND THEN
				INSERT INTO Elements (Creator_ID,ElementType_ID) VALUES (input_Profile_ID, (SELECT ElementType_ID FROM ElementTypes WHERE ElementType='ProfileImageElement'));
				IF FOUND THEN
					INSERT INTO ProfileImage_Elements VALUES (currval('seq_Elements_Element_ID'), input_ImageFileHash, input_MIMEType);
				END IF;
				
				output_Successful:=FOUND;
				
				SELECT INTO 
				output_Element_ID, output_ElementType_ID, output_ElementType, output_ElementPackagePath
				Elements.Element_ID, Elements.ElementType_ID, ElementTypes.ElementType, ElementTypes.ElementPackagePath
				FROM Elements INNER JOIN ElementTypes ON Elements.ElementType_ID=ElementTypes.ElementType_ID
				WHERE Elements.Element_ID=currval('seq_Elements_Element_ID');
			ELSE
				output_Successful:=FALSE;
			END IF;
		ELSE
			output_Successful:=FALSE;
		END IF;
		
	END;
$setProfileImage$ LANGUAGE 'plpgsql';

CREATE OR REPLACE FUNCTION removeProfileImage(input_ProfileCreator BIGINT, input_Profile_ID BIGINT, OUT output_Successful BOOLEAN) AS $removeProfileImage$
	DECLARE
		internal_Element_ID BIGINT;
	BEGIN
		SELECT INTO
		internal_Element_ID 
		ProfileImage_Elements.Element_ID 
		FROM ProfileImage_Elements  INNER JOIN Elements ON ProfileImage_Elements.Element_ID=Elements.Element_ID INNER JOIN Profiles ON Elements.Creator_ID=Profiles.Profile_ID
		WHERE Profiles.Profile_ID=input_Profile_ID AND Profiles.ProfileCreator=input_ProfileCreator;
		
		IF FOUND THEN
			DELETE FROM Elements WHERE Element_ID=internal_Element_ID;
			output_Successful:=FOUND;
		END IF;
	END;
$removeProfileImage$ LANGUAGE 'plpgsql';

CREATE OR REPLACE FUNCTION removeImage(input_FileHash TEXT, input_MIMEType TEXT, input_User_ID BIGINT, OUT output_Successful BOOLEAN) AS $removeImage$
	DECLARE
	
	BEGIN
		-- When the file is removed, ownership is removed not the file.
		DELETE FROM FileOwners WHERE FileHash=input_FileHash AND MIMEType=input_MIMEType AND User_ID=input_User_ID;
		
		output_Successful:=FOUND;
		
	END;
$removeImage$ LANGUAGE 'plpgsql';

CREATE OR REPLACE FUNCTION getElementInstance(input_Element_ID BIGINT, OUT output_ElementType_ID BIGINT, OUT output_Creator_ID BIGINT, OUT output_ElementPackagePath TEXT) AS $getElementInstance$
	DECLARE
	
	BEGIN
		-- There is going to have to be a lot of these . . . based on each ElementType possibly.
		-- Need to spit back more information
		-- ElementType
	
		SELECT INTO
		output_ElementType_ID, output_Creator_ID, output_ElementPackagePath
		Elements.ElementType_ID, Creator_ID, ElementPackagePath
		FROM Elements INNER JOIN ElementTypes ON Elements.ElementType_ID=ElementTypes.ElementType_ID
		WHERE Element_ID=input_Element_ID;
	END;
$getElementInstance$ LANGUAGE 'plpgsql';

-- Used so far for Profile, Friends, Blog
CREATE OR REPLACE FUNCTION createNewElement(input_Profile_ID BIGINT, input_ElementType_ID BIGINT, OUT output_Successful BOOLEAN, OUT output_Element_ID BIGINT, OUT output_ElementType_ID BIGINT, OUT output_ElementType TEXT, OUT output_ElementPackagePath TEXT) AS $GenericElements$
	DECLARE
	
	BEGIN
		INSERT INTO Elements (Creator_ID,ElementType_ID) VALUES (input_Profile_ID, input_ElementType_ID);
		output_Successful:=FOUND;
		
		SELECT INTO 
		output_Element_ID, output_ElementType_ID, output_ElementType, output_ElementPackagePath
		Elements.Element_ID, Elements.ElementType_ID, ElementTypes.ElementType, ElementTypes.ElementPackagePath
		FROM Elements INNER JOIN ElementTypes ON Elements.ElementType_ID=ElementTypes.ElementType_ID
		WHERE Elements.Element_ID=currval('seq_Elements_Element_ID');
		
--	EXCEPTION
	
	END;
$GenericElements$ LANGUAGE 'plpgsql';

-- Used for ProfileImageElements
CREATE OR REPLACE FUNCTION createNewElement(input_Profile_ID BIGINT, input_ElementType_ID BIGINT, input_ImageFileHash TEXT, input_MIMEType TEXT, OUT output_Successful BOOLEAN, OUT output_Element_ID BIGINT, OUT output_ElementType_ID BIGINT, OUT output_ElementType TEXT, OUT output_ElementPackagePath TEXT) AS $ProfileImageElements$
	DECLARE
	
	BEGIN
		INSERT INTO Elements (Creator_ID,ElementType_ID) VALUES (input_Profile_ID, input_ElementType_ID);
		IF FOUND THEN
			INSERT INTO ProfileImage_Elements VALUES (currval('seq_Elements_Element_ID'), input_ImageFileHash, input_MIMEType);
		END IF;
		
		output_Successful:=FOUND;
		
		SELECT INTO 
		output_Element_ID, output_ElementType_ID, output_ElementType, output_ElementPackagePath
		Elements.Element_ID, Elements.ElementType_ID, ElementTypes.ElementType, ElementTypes.ElementPackagePath
		FROM Elements INNER JOIN ElementTypes ON Elements.ElementType_ID=ElementTypes.ElementType_ID
		WHERE Elements.Element_ID=currval('seq_Elements_Element_ID');
		
--	EXCEPTION
	
	END;
$ProfileImageElements$ LANGUAGE 'plpgsql';

-- Used for ImageElements
CREATE OR REPLACE FUNCTION createNewElement(input_Profile_ID BIGINT, input_ElementType_ID BIGINT, input_ImageFileHash TEXT, input_MIMEType TEXT, input_Title TEXT, input_Summery TEXT, OUT output_Successful BOOLEAN, OUT output_Element_ID BIGINT, OUT output_ElementType_ID BIGINT, OUT output_ElementType TEXT, OUT output_ElementPackagePath TEXT) AS $ImageElements$
	DECLARE
	
	BEGIN
		INSERT INTO Elements (Creator_ID,ElementType_ID) VALUES (input_Profile_ID, input_ElementType_ID);
		IF FOUND THEN
			INSERT INTO Image_Elements VALUES (currval('seq_Elements_Element_ID'), input_ImageFileHash, input_MIMEType, input_Title, input_Summery);
		END IF;
		
		output_Successful:=FOUND;
		
		SELECT INTO 
		output_Element_ID, output_ElementType_ID, output_ElementType, output_ElementPackagePath
		Elements.Element_ID, Elements.ElementType_ID, ElementTypes.ElementType, ElementTypes.ElementPackagePath
		FROM Elements INNER JOIN ElementTypes ON Elements.ElementType_ID=ElementTypes.ElementType_ID
		WHERE Elements.Element_ID=currval('seq_Elements_Element_ID');
		
--	EXCEPTION
	
	END;
$ImageElements$ LANGUAGE 'plpgsql';

CREATE OR REPLACE FUNCTION getImageFile(input_FileHash TEXT, input_MIMEType TEXT, OUT output_File TEXT, OUT output_FileSize BIGINT, OUT output_DateUploaded TIMESTAMP, OUT output_Uploader BIGINT, OUT output_FileName TEXT, OUT output_ImageDimensionX SMALLINT, OUT output_ImageDimensionY SMALLINT) AS $getImageFile$
	DECLARE
	
	BEGIN
		SELECT INTO 
		output_File, 	output_FileSize, 	output_DateUploaded, 	output_Uploader, 	output_FileName, 	output_ImageDimensionX,					output_ImageDimensionY
		Files.File, 	Files.FileSize, 	Files.DateUploaded,		Files.Uploader, 	Files.FileName, 	ImageFiles.ImageDimensionX,	ImageFiles.ImageDimensionY
		FROM Files 
			INNER JOIN ImageFiles ON Files.FileHash=ImageFiles.FileHash AND Files.MIMEType=ImageFiles.MIMEType
		WHERE Files.FileHash=input_FileHash AND Files.MIMEType=input_MIMEType; 
	END;
$getImageFile$ LANGUAGE 'plpgsql';

CREATE OR REPLACE FUNCTION insertNewFile(input_FileHash TEXT, input_File TEXT, input_MIMEType TEXT, input_FileSize BIGINT, input_Uploader BIGINT, input_FileName TEXT, OUT output_FileNew BOOLEAN, OUT output_DateUploaded TIMESTAMP ) AS $insertNewFile$
	DECLARE
		
	BEGIN
		INSERT INTO Files (FileHash,File,MIMEType,FileSize,Uploader,FileName) VALUES (input_FileHash, input_File, input_MIMEType, input_Filesize, input_Uploader, input_FileName);
		output_FileNew:=TRUE;
		
		SELECT INTO 
		output_DateUploaded
		DateUploaded
		FROM Files
		WHERE FileHash=input_FileHash AND MIMEType=input_MIMEType;
		
	EXCEPTION
		WHEN unique_violation THEN
			output_FileNew:=FALSE;
	END;
$insertNewFile$ LANGUAGE 'plpgsql';

CREATE OR REPLACE FUNCTION newFileOwner(input_FileHash TEXT, input_MIMEType TEXT, input_User_ID BIGINT, OUT output_Successful BOOLEAN) AS $newFileOwner$
	DECLARE
	
	BEGIN
		INSERT INTO FileOwners VALUES (input_FileHash, input_MIMEType, input_User_ID);
		output_Successful:=TRUE;
	EXCEPTION
		WHEN unique_violation THEN
			-- Later need a way of saying that the user already owns the file.
			output_Successful:=FALSE;
			-- Need something for any exception that occurs.
	END;
$newFileOwner$ LANGUAGE 'plpgsql';
		

CREATE OR REPLACE FUNCTION createNewImageFile(input_FileHash TEXT, input_File TEXT, input_MIMEType TEXT, input_FileSize BIGINT, input_Uploader BIGINT, input_FileName TEXT, input_ImageDimensionX SMALLINT, input_ImageDimensionY SMALLINT, OUT output_Successful BOOLEAN, OUT output_DateUploaded TIMESTAMP) AS $createNewImageFile$
	DECLARE
		internal_FileNew BOOLEAN;
	BEGIN
		SELECT INTO 
		internal_FileNew, output_DateUploaded
		output_FileNew, output_DateUploaded
		FROM insertNewFile(input_FileHash, input_File, input_MIMEType, input_FileSize, input_Uploader, input_FileName);
		
		IF internal_FileNew THEN
			INSERT INTO ImageFiles VALUES (input_FileHash, input_MIMEType, input_ImageDimensionX, input_ImageDimensionY);
		END IF;		
		
		IF FOUND THEN
			SELECT INTO
			output_Successful
			output_Successful
			FROM newFileOwner(input_FileHash, input_MIMEType, input_Uploader);
		END IF;
	END;
$createNewImageFile$ LANGUAGE 'plpgsql';

CREATE OR REPLACE FUNCTION getProfilePageLayout(input_Profile_ID BIGINT, OUT output_ProfilePageLayout TEXT) AS $getProfilePageLayout$
	DECLARE
	
	BEGIN
		SELECT INTO 
		output_ProfilePageLayout
		ProfilePageLayout
		FROM Profiles
		WHERE Profile_ID=input_Profile_ID;
	END;
$getProfilePageLayout$ LANGUAGE 'plpgsql';

CREATE OR REPLACE FUNCTION setProfilePageLayout(input_Profile_ID BIGINT, input_ProfilePageLayout TEXT, OUT output_Successful BOOLEAN) AS $setProfilePageLayout$
	DECLARE
	
	BEGIN
		UPDATE Profiles SET ProfilePageLayout=input_ProfilePageLayout WHERE Profile_ID=input_Profile_ID;
		
		output_Successful:=FOUND;
	END;
$setProfilePageLayout$ LANGUAGE 'plpgsql';

CREATE OR REPLACE FUNCTION setResidence (input_CountryISO TEXT, input_AddressCreator BIGINT, input_US_AddressLine1 TEXT, input_US_AddressLine2 TEXT, input_US_CityTown TEXT, input_US_StateProvince_ID TEXT, input_US_PostalCode1 TEXT, input_US_PostalCode2 TEXT, OUT output_Successful BOOLEAN) AS $setResidence$
	DECLARE
		internal_Profile_ID BIGINT;
	BEGIN
		
		UPDATE UserAddresses SET LegalAddress=FALSE, UserAddressLabel='Another Address' WHERE User_ID=input_AddressCreator;
		
		INSERT INTO Addresses (CountryISO, AddressCreator) VALUES (input_CountryISO, input_AddressCreator);
		
		INSERT INTO US_Addresses VALUES (currval('seq_Addresses_Address_ID'), input_US_AddressLine1, input_US_AddressLine2, input_US_CityTown, input_US_StateProvince_ID, input_US_PostalCode1, input_US_PostalCode2);

		INSERT INTO UserAddresses VALUES (input_AddressCreator, currval('seq_Addresses_Address_ID'), TRUE, 'Residence'); 
		
	END;
$setResidence$ LANGUAGE 'plpgsql';	

CREATE OR REPLACE FUNCTION setUS_HomeTown (input_User_ID BIGINT, input_CountryISOCode TEXT, input_US_CityTown TEXT, input_US_StateProvince_ID TEXT, OUT output_Successful BOOLEAN) AS $setHomeTown$
-- For now this will work with User_ID later it will need a profile_ID for the specific profile page.
	DECLARE
		internal_Profile_ID BIGINT;	
	BEGIN
		SELECT INTO
		internal_profile_ID
		IndividualProfiles.Profile_ID
		FROM Users INNER JOIN (Profiles INNER JOIN IndividualProfiles ON Profiles.Profile_ID=IndividualProfiles.Profile_ID) ON Users.User_ID=Profiles.ProfileCreator 
		WHERE User_ID=input_User_ID AND MainProfile=TRUE;
	
		INSERT INTO HomeTowns VALUES (internal_Profile_ID, input_CountryISOCode);
		
		INSERT INTO US_HomeTowns VALUES (internal_Profile_ID, input_US_CityTown, input_US_StateProvince_ID);
		
		output_Successful:=TRUE;
		
	EXCEPTION
		WHEN unique_violation THEN
			UPDATE US_HomeTowns SET US_CityTown=input_US_CityTown, US_StateProvince_ID=input_US_StateProvince_ID WHERE Profile_ID=internal_Profile_ID;
			output_Successful:=TRUE;
	END;
$setHomeTown$ LANGUAGE 'plpgsql'; 

CREATE OR REPLACE FUNCTION viewBlog(input_Blog_ID BIGINT, OUT output_BlogWriter BIGINT, OUT output_DateBlogCreated TIMESTAMP, OUT output_DateEdited TIMESTAMP, OUT output_BlogTitle TEXT, OUT output_BlogContent TEXT) AS $viewBlog$
	DECLARE
	
	BEGIN
		SELECT INTO 
		output_BlogWriter, output_DateBlogCreated, output_DateEdited, output_BlogTitle, output_BlogContent
		BlogWriter, DateBlogCreated, DateEdited, BlogTitle, BlogContent
		FROM Blogs
			INNER JOIN BlogEntries ON Blogs.Blog_ID=BlogEntries.Blog_ID
		WHERE Blogs.Blog_ID=input_Blog_ID 
			AND DateEdited=(SELECT MAX(DateEdited) FROM BlogEntries WHERE Blog_ID=input_Blog_ID);
	END;
$viewBlog$ LANGUAGE 'plpgsql';

CREATE OR REPLACE FUNCTION editBlog(input_Blog_ID BIGINT, input_BlogTitle TEXT, input_BlogContent TEXT, OUT output_Successful BOOLEAN) AS $editBlog$
	DECLARE
		
	BEGIN
		INSERT INTO BlogEntries (Blog_ID, BlogTitle, BlogContent) VALUES (input_Blog_ID, input_BlogTitle, input_BlogContent);
		output_Successful:=FOUND;
	END;
$editBlog$ LANGUAGE 'plpgsql';

CREATE OR REPLACE FUNCTION deleteBlog(input_Blog_ID BIGINT, OUT output_Successful BOOLEAN) AS $deleteBlog$
	DECLARE
	
	BEGIN
		DELETE FROM Blogs 
		WHERE Blog_ID=input_Blog_ID;
		
		output_Successful:=FOUND;
		
	END;
$deleteBlog$ LANGUAGE 'plpgsql';

CREATE OR REPLACE FUNCTION createNewBlog(input_User_ID BIGINT, input_BlogTitle TEXT, input_BlogContent TEXT, OUT output_Successful BOOLEAN) AS $createNewBlog$
	DECLARE
	
	BEGIN
		output_Successful:=TRUE;
		INSERT INTO Blogs (BlogWriter) VALUES (input_User_ID);
		IF FOUND THEN
			INSERT INTO BlogEntries (Blog_ID, BlogTitle, BlogContent) VALUES (currval('seq_Blogs_Blog_ID'), input_BlogTitle, input_BlogContent);
			IF FOUND IS NOT TRUE THEN
				output_Successful:=FOUND;
			END IF;
		ELSE
			output_Successful:=FOUND;
		END IF;
		
	END;
$createNewBlog$ LANGUAGE 'plpgsql';

CREATE OR REPLACE FUNCTION getEmailAddress(input_Profile_ID BIGINT, OUT output_EmailAddress TEXT) AS $getEmailAddress$
	DECLARE
		
	BEGIN
		SELECT INTO
		output_EmailAddress
		EmailAddress
		FROM UserEmailAddresses
		WHERE User_ID=(SELECT ProfileCreator FROM Profiles WHERE Profile_ID=input_Profile_ID) AND UserMainEmailAddress=TRUE;
	END;
	
$getEmailAddress$ LANGUAGE 'plpgsql';

CREATE OR REPLACE FUNCTION setEmailAddress(input_User_ID BIGINT, input_EmailAddress TEXT, OUT output_Successful BOOLEAN) AS $setEmailAddress$
	DECLARE
		
	BEGIN
		PERFORM EmailAddress FROM UserEmailAddresses WHERE EmailAddress=input_EmailAddress;
		-- Checks to see if the email address exists in the user table.
		-- If it does then the address is being used by another user and shit failes.
		IF FOUND THEN
			output_Successful:=FALSE;
		ELSE
			-- Removes old email address from UserEmailAddresses
			DELETE FROM UserEmailAddresses WHERE User_ID=input_User_ID AND UserMainEmailAddress=TRUE;
			
			PERFORM EmailAddress FROM EmailAddresses WHERE EmailAddress=input_EmailAddress;
			IF FOUND IS FALSE THEN
				INSERT INTO EmailAddresses (EmailAddress) 
				VALUES (input_EmailAddress);
			END IF;
			
			INSERT INTO UserEmailAddresses (User_ID,EmailAddress,UserMainEmailAddress)
			VALUES (input_User_ID,input_EmailAddress,TRUE);
			
			output_Successful:=TRUE;
		END IF;
		
	EXCEPTION
		WHEN unique_violation THEN
			output_Successful:=FALSE;
		
	END;
$setEmailAddress$ LANGUAGE 'plpgsql';

CREATE OR REPLACE FUNCTION dropSexualPreference(input_User_ID BIGINT, input_Gender TEXT, OUT output_Successful BOOLEAN, OUT output_NotThere BOOLEAN) AS $dropSexualPreference$
	DECLARE
	
	BEGIN
		output_Successful:=TRUE;
		output_NotThere:=FALSE;
		DELETE FROM SexualPreferences WHERE Profile_ID=(SELECT IndividualProfiles.Profile_ID FROM Users INNER JOIN (Profiles INNER JOIN IndividualProfiles ON Profiles.Profile_ID=IndividualProfiles.Profile_ID) ON Users.User_ID=Profiles.ProfileCreator WHERE User_ID=input_User_ID AND MainProfile=TRUE) AND Gender=input_Gender;
		output_Successful:=FOUND;
		output_NotThere:=NOT FOUND;
	END;
$dropSexualPreference$ LANGUAGE 'plpgsql';

CREATE OR REPLACE FUNCTION getSexualPreference(input_User_ID BIGINT) RETURNS SETOF TEXT AS $getSexualPreference$
	DECLARE
		output_Gender RECORD;
	BEGIN
		FOR output_Gender IN SELECT Gender FROM SexualPreferences WHERE Profile_ID=(SELECT IndividualProfiles.Profile_ID FROM Users INNER JOIN (Profiles INNER JOIN IndividualProfiles ON Profiles.Profile_ID=IndividualProfiles.Profile_ID) ON Users.User_ID=Profiles.ProfileCreator WHERE User_ID=input_User_ID AND MainProfile=TRUE) LOOP
		    RETURN NEXT output_Gender;
		END LOOP;
	
		RETURN;

	END;
$getSexualPreference$ LANGUAGE 'plpgsql';

CREATE OR REPLACE FUNCTION addSexualPreference(input_User_ID BIGINT, input_Gender TEXT, OUT output_Successful BOOLEAN, OUT output_AlreadyThere BOOLEAN) AS $addSexualPreference$
	DECLARE
	
	BEGIN
		output_AlreadyThere:=FALSE;
		INSERT INTO SexualPreferences
		VALUES ((SELECT IndividualProfiles.Profile_ID FROM Users INNER JOIN (Profiles INNER JOIN IndividualProfiles ON Profiles.Profile_ID=IndividualProfiles.Profile_ID) ON Users.User_ID=Profiles.ProfileCreator WHERE User_ID=input_User_ID AND MainProfile=TRUE), input_Gender);
		output_Successful:=FOUND;
		
	EXCEPTION
		WHEN unique_violation THEN
			output_Successful:=FALSE;
			output_AlreadyThere:=TRUE;
	END;
$addSexualPreference$ LANGUAGE 'plpgsql';

CREATE OR REPLACE FUNCTION dropLookingFor(input_User_ID BIGINT, input_LookingForOption TEXT, OUT output_Successful BOOLEAN, OUT output_NotThere BOOLEAN) AS $dropLookingFor$
	DECLARE
	
	BEGIN
		output_Successful:=TRUE;
		output_NotThere:=FALSE;
		DELETE FROM LookingFor WHERE Profile_ID=(SELECT IndividualProfiles.Profile_ID FROM Users INNER JOIN (Profiles INNER JOIN IndividualProfiles ON Profiles.Profile_ID=IndividualProfiles.Profile_ID) ON Users.User_ID=Profiles.ProfileCreator WHERE User_ID=input_User_ID AND MainProfile=TRUE) AND LookingForOption=input_LookingForOption;
		output_Successful:=FOUND;
		output_NotThere:=NOT FOUND;
	END;
$dropLookingFor$ LANGUAGE 'plpgsql';

CREATE OR REPLACE FUNCTION getLookingFor(input_User_ID BIGINT) RETURNS SETOF TEXT AS $getLookingFor$
	DECLARE
		output_LookingForOption RECORD;--LookingFor.LookingForOption%TYPE;
	BEGIN
		FOR output_LookingForOption IN SELECT LookingForOption FROM LookingFor WHERE Profile_ID=(SELECT IndividualProfiles.Profile_ID FROM Users INNER JOIN (Profiles INNER JOIN IndividualProfiles ON Profiles.Profile_ID=IndividualProfiles.Profile_ID) ON Users.User_ID=Profiles.ProfileCreator WHERE User_ID=input_User_ID AND MainProfile=TRUE) LOOP
		    RETURN NEXT output_LookingForOption;
		END LOOP;
	
		RETURN;

	END;
$getLookingFor$ LANGUAGE 'plpgsql';

CREATE OR REPLACE FUNCTION addLookingFor(input_User_ID BIGINT, input_LookingForOption TEXT, OUT output_Successful BOOLEAN, OUT output_AlreadyThere BOOLEAN) AS $addLookingFor$
	DECLARE
	
	BEGIN
		output_AlreadyThere:=FALSE;
		INSERT INTO LookingFor 
		VALUES ((SELECT IndividualProfiles.Profile_ID FROM Users INNER JOIN (Profiles INNER JOIN IndividualProfiles ON Profiles.Profile_ID=IndividualProfiles.Profile_ID) ON Users.User_ID=Profiles.ProfileCreator WHERE User_ID=input_User_ID AND MainProfile=TRUE), input_LookingForOption);
		output_Successful:=FOUND;
		
	EXCEPTION
		WHEN unique_violation THEN
			output_Successful:=FALSE;
			output_AlreadyThere:=TRUE;
	END;
$addLookingFor$ LANGUAGE 'plpgsql';

CREATE OR REPLACE FUNCTION getSex(input_Profile_ID BIGINT, OUT output_Sex TEXT) AS $getSex$
	DECLARE
	
	BEGIN
		SELECT INTO 
		output_SEX
		Gender
		FROM Users 
		WHERE User_ID=(SELECT ProfileCreator FROM Profiles WHERE Profile_ID=input_Profile_ID);
	END;
$getSex$ LANGUAGE 'plpgsql';

CREATE OR REPLACE FUNCTION setSex(input_User_ID BIGINT, input_Gender TEXT, OUT output_Successful BOOLEAN) AS $setSex$
	-- This will write the user's gender into the user table.
	-- For gag profiles we will need another function.
	DECLARE
	
	BEGIN
		-- Since user creation makes a gender, and it cannot be null, only an update script is required here.
		UPDATE Users SET Gender=input_Gender WHERE User_ID=input_User_ID;
		output_Successful:=FOUND;
		
--	EXCEPTION
		-- This may be needed for error catching.
	END;

$setSex$ LANGUAGE 'plpgsql';

CREATE OR REPLACE FUNCTION getBirthDate(input_Profile_ID BIGINT, OUT output_BirthDate DATE) AS $getBirthDate$
	-- This function grabs a persons birthdate from their users table.
	-- For gag profiles later we need a seperate function.
	DECLARE
	
	BEGIN
		SELECT INTO 
		output_BirthDate
		BirthDate 
		FROM Users
		WHERE User_ID=(SELECT ProfileCreator FROM Profiles WHERE Profile_ID=input_Profile_ID);
	END;
	
$getBirthDate$ LANGUAGE 'plpgsql';

CREATE OR REPLACE FUNCTION setBirthDate(input_User_ID BIGINT, input_BirthDate DATE, OUT output_Successful BOOLEAN) AS $setBirthDate$
	-- This will write the user's birthdate into the user table.
	-- For gag profiles we will need another function.
	DECLARE
	
	BEGIN
		-- Since user creation makes a gender, and it cannot be null, only an update script is required here.
		UPDATE Users SET BirthDate=input_BirthDate WHERE User_ID=input_User_ID;
		output_Successful:=FOUND;
		
--	EXCEPTION
		-- This may be needed for error catching.
	END;

$setBirthDate$ LANGUAGE 'plpgsql';

CREATE OR REPLACE FUNCTION getUserAssociationLevel (input_User_ID BIGINT, OUT output_UserAssociationLevel SMALLINT) AS $getUserAssociationLevel$
	DECLARE
	
	BEGIN
		SELECT INTO 
		output_UserAssociationLevel
		UserAssociationLevel
		FROM Users
		WHERE User_ID=input_User_ID;
	END;
$getUserAssociationLevel$ LANGUAGE 'plpgsql';

CREATE OR REPLACE FUNCTION setNumberOfInvites (input_User_ID BIGINT, input_NumberOfInvites SMALLINT, OUT output_InvitesSet BOOLEAN) AS $setNumberOfInvites$
	DECLARE
			
	BEGIN
		UPDATE Users SET NumberOfInvites=input_NumberOfInvites WHERE User_ID=input_User_ID;
		output_InvitesSet:=FOUND;
	END;

$setNumberOfInvites$ LANGUAGE 'plpgsql';

CREATE OR REPLACE FUNCTION markReferral_IDAsUsed(input_Referral_ID TEXT, OUT output_Successful BOOLEAN) AS $markReferral_IDAsUsed$
	DECLARE
	
	BEGIN
		UPDATE UserInvitations SET InvitationsAccepted=TRUE WHERE Referral_ID=input_Referral_ID;
		output_Successful:=FOUND;
	END;
	
$markReferral_IDAsUsed$ LANGUAGE 'plpgsql';

CREATE OR REPLACE FUNCTION getNumberOfInvitesLeft (input_User_ID BIGINT, OUT output_NumberOfInvites SMALLINT) AS $getNumberOfInvitesLeft$
	
	DECLARE
		
	BEGIN
		SELECT INTO 
		output_NumberOfInvites
		NumberOfInvites
		FROM Users
		WHERE User_ID=input_User_ID;
	END;
	
$getNumberOfInvitesLeft$ LANGUAGE 'plpgsql';

CREATE OR REPLACE FUNCTION isReferral_IDUnique (input_Referral_ID TEXT, OUT output_ReferralUnique BOOLEAN) AS $isReferral_IDUnique$
-- If the referal code exists then referal is not unique
	DECLARE
	
	BEGIN
		PERFORM Referral_ID FROM UserInvitations WHERE Referral_ID=input_Referral_ID;
		
		IF FOUND THEN
			output_ReferralUnique:=FALSE;
		ELSE
			output_ReferralUnique:=TRUE;		
		END IF;	
	END;
$isReferral_IDUnique$ LANGUAGE 'plpgsql';

CREATE OR REPLACE FUNCTION checkReferral_ID (input_Referral_ID TEXT, OUT output_ReferralOK BOOLEAN) AS $checkReferral_ID$
-- Determines if a Referral Id belongs to a Referral and that has not been accepted.
	DECLARE
	
	BEGIN
		PERFORM Referral_ID FROM UserInvitations WHERE Referral_ID=input_Referral_ID AND InvitationAccepted=FALSE;
		
		IF FOUND THEN
			output_ReferralOK:=TRUE;
		ELSE
			output_ReferralOK:=FALSE;
		END IF;
	END;

$checkReferral_ID$ LANGUAGE 'plpgsql';

CREATE OR REPLACE FUNCTION addUserInvite(input_User_ID BIGINT, input_EmailAddress TEXT, input_Referral_ID TEXT, OUT output_InviteSuccessful BOOLEAN) AS $addUserInvite$
	DECLARE
	
	BEGIN
		INSERT INTO UserInvitations (User_ID,EmailAddress,Referral_ID)
		VALUES (input_User_ID, input_EmailAddress, input_Referral_ID);
		
		output_InviteSuccessful:=FOUND;
		
		UPDATE Users 
		SET NumberOfInvites=NumberOfInvites-1 
		WHERE User_ID=input_User_ID;
		
	EXCEPTION
		WHEN unique_violation THEN
			output_InviteSuccessful:=FALSE;	
	END;
$addUserInvite$ LANGUAGE 'plpgsql';

CREATE OR REPLACE FUNCTION createNewUser (input_UserName TEXT, input_PasswordHash Text, input_EmailAddress TEXT, input_FirstName TEXT, input_LastName TEXT, input_Gender TEXT, input_BirthDate DATE, input_Referral_ID TEXT, OUT output_UserCreated BOOLEAN, OUT output_User_ID BIGINT, OUT output_UserUnique BOOLEAN, OUT output_EmailUnique BOOLEAN) AS $createNewUser$
	-- Until I am able to test more shit with exceptions and get definative answers on how they work when you use Begin Blocks within
	-- Begin blocks, we'll test that certian fields are unique at the start. This slows things down a bit, but since user creation is only happening once
	-- and not as often as other stuff, it should be ok for private beta.

	-- Need user reference number in here somewhere.
	-- Also need to calculate the UserAssociationLevel based on their refernece number.
	DECLARE
		internal_AdultPageCheck BOOLEAN;
		internal_EmailExistsCheck BOOLEAN;
		
	BEGIN
		output_UserCreated:=TRUE;
		output_UserUnique:=TRUE;
		output_EmailUnique:=TRUE;
		output_User_ID:=NULL;

		-- Coding question, should I label output_UserCreated as false here or use some logic to do it after?
		PERFORM PageName FROM PageNames WHERE PageName=input_UserName;
		-- If the username is at all used in pagenames it can't be valid.
		IF FOUND THEN
			output_UserUnique:=FALSE;
			output_UserCreated:=FALSE;
		END IF;
		
		PERFORM EmailAddress FROM EmailAddresses WHERE EmailAddress=input_EmailAddress;
		-- Checks to see if the email address exists, if it does make sure its not associated with any user.
		-- Might want to see if its associated with a user as a main email instead but this can be debated after email
		-- is better thought through.
		IF FOUND THEN
			internal_EmailExistsCheck:=TRUE;
			PERFORM EmailAddress FROM UserEmailAddresses WHERE EmailAddress=input_EmailAddress;
			-- Later will have to check to make sure the email does not belong with profiles.
			IF FOUND THEN
				output_EmailUnique:=FALSE;
				output_UserCreated:=FALSE;
			END IF;
		ELSE
			internal_EmailExistsCheck:=FALSE;
		END IF;
		
		PERFORM Referral_ID FROM UserInvitations WHERE Referral_ID=input_Referral_ID AND InvitationAccepted=TRUE;
		IF FOUND THEN
			output_UserCreated:=FALSE;
		END IF;
		
		IF output_UserCreated THEN
			INSERT INTO PageNames
			VALUES (input_UserName);
			
			-- There are a bunch of fields that are filled with crap right now.  Need to add in later proper functions to fill them.
			-- UserAssociation Level needs a select to fill it.
			INSERT INTO Users (UserType,UserName,UserPasswordHash,PassPhraseQuestion,PassPhraseAnswer,BirthDate,ProfessionalPrefix,NamePrefix,FirstName,MiddleName,LastName,NameSuffix,ProfessionalSuffix,Gender,UserAssociationLevel) 
			VALUES ('NormalUser', input_UserName, input_PasswordHash, 'Huh?', 'What?', input_BirthDate, NULL, NULL, input_FirstName, NULL, input_LastName, NULL, NULL, input_Gender,(SELECT UserAssociationLevel+1 FROM Users WHERE User_ID=(SELECT User_ID FROM UserInvitations WHERE Referral_ID=input_Referral_ID)));
			
			-- Might be a faster way to do this . . . 
			SELECT INTO
			output_User_ID
			User_ID
			FROM Users
			WHERE User_ID=currval('seq_Users_User_ID');
			
			UPDATE UserInvitations SET InvitationAccepted=TRUE WHERE Referral_ID=input_Referral_ID;
			
			INSERT INTO UserReferences
			VALUES (input_Referral_ID, currval('seq_Users_User_ID'));
			
			IF internal_EmailExistsCheck IS FALSE THEN
				-- Insert new email into the email table.
				INSERT INTO EmailAddresses (EmailAddress) 
				VALUES (input_EmailAddress);
			END IF;
			
			INSERT INTO UserEmailAddresses (User_ID,EmailAddress,UserMainEmailAddress) 
			VALUES (currval('seq_Users_User_ID'), input_EmailAddress, TRUE);
			
			-- This might be faster using the birthdate value in the function rather than hitting the db.
			SELECT INTO internal_AdultPageCheck
			(AGE(CURRENT_TIMESTAMP,(SELECT BirthDate FROM Users WHERE User_ID=currval('seq_Users_User_ID'))) >= INTERVAL '18 years');
				
			-- Create new profile page.
			INSERT INTO Profiles (ProfileCreator,ProfileName,AdultProfile,ProfileType) 
			VALUES (currval('seq_Users_User_ID'), input_UserName, internal_AdultPageCheck, 'Individual');
			
--			IF internal_AdultPageCheck THEN
--				INSERT INTO AdultPages 
--				VALUES ();
--			END IF;

			-- Create individual profile page set as main.
			INSERT INTO IndividualProfiles 
			VALUES (currval('seq_Profiles_Profile_ID'), TRUE);
			
		END IF;
	
	EXCEPTION
		-- This bit doesn't work for any exception.
		-- Re go through exception list and find a catch all if there is one.
		WHEN raise_exception THEN
			output_UserCreated:=FALSE;
			-- This is for if other shit fucks up for any reason.
			-- Then the function fails and doesn't poop on us.
	END;

$createNewUser$ LANGUAGE 'plpgsql';

-- Number of rows deleted.
-- Its in the accountDao
CREATE OR REPLACE FUNCTION userLogin ( input_UserName TEXT, input_UserPasswordHash TEXT, input_IPAddress TEXT, input_UserAgent TEXT, OUT output_User_ID BIGINT) AS $userLogin$ -- Returned a bigint before.

-- Need to grab functions that will be reused in this and create them.

	DECLARE		
		internal_Successful LoginAttempts.Successful%TYPE;
		
	BEGIN
		SELECT INTO output_User_ID 
		User_ID 
		FROM Users 
		WHERE UserName=input_UserName and UserPassWordHash=input_UserPasswordHash and active = true;
		
		IF (output_User_ID IS NULL) THEN
			internal_Successful:=FALSE;
		ELSE
			internal_Successful:=TRUE;
		END IF;

		INSERT INTO LoginAttempts (UserName,IPAddress,UserAgent,Successful) VALUES (input_UserName,CAST(Input_IPAddress AS INET),input_UserAgent,internal_Successful);
		
		IF (internal_Successful) THEN
			INSERT INTO SuccessfulLogins VALUES (currval('seq_LoginAttempts_LoginAttempt_ID'),output_User_ID);
		ELSE
			INSERT INTO UnsuccessfulLogins VALUES (currval('seq_LoginAttempts_LoginAttempt_ID'),input_UserPasswordHash);
		END IF;
	
	END;
	
$userLogin$ LANGUAGE 'plpgsql';

-- User enters the site.  First time they come in php is like yo gimme cookie. 
-- they don't have a session, php generates a session and makes an ID up.  Sets a session cookie and puts the session ID in the cookie, then sends it 
-- to the user.  Everytime the user enters the site it will use the session id to

-- If successful return boolean of success.

-- 32 digits.
-- 4561ff7961dc7b2d9a845c6bfb7d1323
-- ad19a56c4d09191d6e58b01b87225468
-- d152c2fb7115ebf1b353235ad1292fa7
-- True for succeded + false failure.
CREATE OR REPLACE FUNCTION setSession (input_Session_ID TEXT, input_User_ID BIGINT, input_UserAgent TEXT, input_SessionData TEXT) RETURNS BOOLEAN AS $setSession$

	DECLARE
		
	BEGIN
		UPDATE Sessions
		SET SessionData = input_SessionData, DateAccessed = CURRENT_TIMESTAMP
		WHERE Session_ID = input_Session_ID;
		
		IF NOT FOUND THEN
			INSERT INTO Sessions (Session_ID, UserAgent, SessionData)
			VALUES (input_Session_ID, input_UserAgent, input_SessionData);
			
			IF input_User_ID NOTNULL THEN
				INSERT INTO IdentifiedSessions (Session_ID, User_ID)
				VALUES (input_Session_ID, input_User_ID);
			END IF;
		END IF;
		
	RETURN FOUND;
	
	END;

$setSession$ LANGUAGE 'plpgsql';

-- Gets data on a session and updates its access date.
CREATE OR REPLACE FUNCTION getSession (input_Session_ID TEXT, OUT output_Session_ID TEXT, OUT output_User_ID BIGINT, OUT output_UserAgent TEXT, OUT output_SessionData TEXT, OUT output_SessionExists BOOLEAN) AS $getSession$

	DECLARE
			
	BEGIN
		UPDATE Sessions
		SET DateAccessed = CURRENT_TIMESTAMP
		WHERE Session_ID = input_Session_ID;

		SELECT INTO 
		output_Session_ID,		output_User_ID,		output_UserAgent, 	output_SessionData 
		Sessions.Session_ID,	User_ID,			UserAgent, 			SessionData
		FROM Sessions LEFT JOIN IdentifiedSessions ON Sessions.Session_ID = IdentifiedSessions.Session_ID
		WHERE Sessions.Session_ID = input_Session_ID;
		
		output_SessionExists:=FOUND;
				
--		SELECT INTO 
--		output_UserAgent, 	output_SessionData 
--		UserAgent, 			SessionData
--		FROM Sessions
--		WHERE Session_ID = inoutput_Session_ID;
		
--		SELECT INTO 
--		output_User_ID
--		User_ID
--		FROM IdentifiedSessions
--		WHERE Session_ID = inoutput_Session_ID;
	
	END;

$getSession$ LANGUAGE 'plpgsql';

--Deletes a specific session from the database
CREATE OR REPLACE FUNCTION deleteSession (input_Session_ID TEXT) RETURNS BOOLEAN AS $deleteSession$

	DECLARE
	
	BEGIN
		DELETE FROM Sessions 
		WHERE Session_ID = input_Session_ID;
	
	RETURN TRUE;
	
	END;

$deleteSession$ LANGUAGE 'plpgsql';

-- Deletes outdated sessions from the database.
-- Should return the number of sessions that are deleted.
-- Should also let the sequence know what session ids are freed up.
CREATE OR REPLACE FUNCTION deleteOldSessions (input_SessionLifeTime INTERVAL) RETURNS BOOLEAN AS $deleteOldSessions$

-- I am going to be passed integer of minutes.
-- I must cast into interval.

	DECLARE
	
	BEGIN
		DELETE FROM Sessions WHERE (CURRENT_TIMESTAMP-DateAccessed)>=input_sessionLifeTime;	
	
	RETURN TRUE;
	
	END;
	
$deleteOldSessions$ LANGUAGE 'plpgsql';-- This first role is what the webservers will use to log in and access/change information in the database.
-- This role cannot create any new tables or the like.
CREATE ROLE WebServer
WITH 
	NOSUPERUSER NOCREATEDB NOCREATEROLE NOINHERIT LOGIN ENCRYPTED PASSWORD 'eglooPassword';
	-- SELECT, INSERT, UPDATE, EXECUTE?, USAGE?
GRANT SELECT ON TABLE NameSuffixes TO WebServer;
GRANT SELECT ON TABLE ProfessionalSuffixes TO WebServer;
GRANT SELECT ON TABLE ProfessionalPrefixes TO WebServer;
GRANT SELECT ON TABLE NamePrefixes TO WebServer;
GRANT SELECT ON TABLE Genders TO WebServer;
GRANT SELECT ON TABLE UserTypes TO WebServer;

GRANT SELECT, INSERT ON TABLE PageNames TO WebServer;
GRANT SELECT, UPDATE ON seq_Users_User_ID TO WebServer;
GRANT SELECT, INSERT, UPDATE ON TABLE Users TO WebServer;
GRANT SELECT, INSERT, UPDATE ON TABLE EmailAddresses TO WebServer;
GRANT SELECT, INSERT, UPDATE ON TABLE UserEmailAddresses TO WebServer;
GRANT SELECT, INSERT, UPDATE ON TABLE VerifyEmailAddress TO WebServer;
GRANT SELECT, INSERT, UPDATE ON TABLE UserInvitations TO WebServer;
GRANT SELECT, INSERT, UPDATE ON TABLE UserReferences TO WebServer;

GRANT SELECT ON TABLE Countries to WebServer;
GRANT SELECT, UPDATE ON seq_Addresses_Address_ID TO WebServer;
GRANT SELECT, INSERT, UPDATE ON TABLE Addresses TO WebServer;
GRANT SELECT, UPDATE ON TABLE US_StateProvinces TO WebServer;
GRANT SELECT, INSERT, UPDATE ON TABLE US_Addresses TO WebServer;
GRANT SELECT, INSERT, UPDATE ON TABLE UserAddresses TO WebServer;

GRANT SELECT, UPDATE ON seq_LoginAttempts_LoginAttempt_ID TO WebServer;
GRANT SELECT, INSERT, UPDATE ON TABLE LoginAttempts TO WebServer;
GRANT SELECT, INSERT ON TABLE SuccessfulLogins TO WebServer;
GRANT SELECT, INSERT ON TABLE UnsuccessfulLogins TO WebServer;
GRANT SELECT, UPDATE, INSERT, DELETE ON TABLE Sessions TO WebServer;
GRANT SELECT, UPDATE, INSERT, DELETE ON TABLE IdentifiedSessions TO WebServer;

GRANT SELECT, UPDATE ON seq_Profiles_Profile_ID TO WebServer;
GRANT SELECT ON TABLE ProfileTypes TO WebServer;
GRANT SELECT, INSERT, UPDATE ON TABLE Profiles TO WebServer;
GRANT SELECT, INSERT, UPDATE ON TABLE AdultProfiles TO WebServer;
GRANT SELECT, INSERT, UPDATE ON TABLE IndividualProfiles TO WebServer;

GRANT SELECT ON TABLE LookingForOptions TO WebServer;
GRANT SELECT, INSERT, DELETE ON TABLE LookingFor TO WebServer;
GRANT SELECT ON TABLE SexualPreferenceOptions TO WebServer;
GRANT SELECT, INSERT, DELETE ON TABLE SexualPreferences TO WebServer;
GRANT SELECT, INSERT, UPDATE, DELETE ON TABLE HomeTowns TO WebServer; -- should be placed in profile.
GRANT SELECT, INSERT, UPDATE, DELETE ON TABLE US_HomeTowns TO WebServer;

GRANT SELECT, UPDATE ON seq_Relationshps_Relationship_ID TO WebServer;
GRANT SELECT, INSERT, UPDATE, DELETE ON TABLE Relationships TO WebServer;
GRANT SELECT ON TABLE BiDirectionalRelationshipTypes TO WebServer;
GRANT SELECT, INSERT, UPDATE, DELETE ON TABLE BiDirectionalRelationships TO WebServer;
GRANT SELECT ON TABLE UniDirectionalRelationshipTypes TO WebServer;
GRANT SELECT, INSERT, UPDATE, DELETE ON TABLE UniDirectionalRelationships TO WebServer;

GRANT SELECT ON ElementPackages TO WebServer;
GRANT SELECT, UPDATE ON seq_ElementTypes_ElementType_ID TO WebServer;
GRANT SELECT ON TABLE ElementTypes TO WebServer;
GRANT SELECT, UPDATE ON seq_Elements_Element_ID TO WebServer;
GRANT SELECT, INSERT, UPDATE ON TABLE Elements TO WebServer;

GRANT SELECT, UPDATE ON seq_Blogs_Blog_ID TO WebServer;
GRANT SELECT, INSERT ON TABLE Blogs TO WebServer; -- Delete needed?
GRANT SELECT, INSERT ON TABLE BlogEntries TO WebServer;

GRANT SELECT ON TABLE MIMETypes TO WebServer;
GRANT SELECT, INSERT, UPDATE, DELETE ON TABLE Files TO WebServer;
GRANT SELECT, INSERT, UPDATE, DELETE ON TABLE FileOwners TO WebServer;
GRANT SELECT, INSERT, UPDATE, DELETE ON TABLE ImageFiles TO WebServer;

GRANT SELECT, INSERT, UPDATE, DELETE ON TABLE Image_Elements TO WebServer;
GRANT SELECT, INSERT, UPDATE, DELETE ON TABLE ProfileImage_Elements TO WebServer;

-- Do we need a role for build/alter scripts . . .? Yes different role.-- Genders
INSERT INTO Genders VALUES ('Male');
INSERT INTO Genders VALUES ('Female');

-- User Types
-- Currently no distiction later there will be.
INSERT INTO UserTypes VALUES ('BetaTester');
INSERT INTO UserTypes VALUES ('Developer');
INSERT INTO UserTypes VALUES ('NormalUser');
INSERT INTO UserTypes VALUES ('Administrator');

-- Profile Types.
-- Group ones aren't set up yet.
INSERT INTO ProfileTypes VALUES ('Individual');
INSERT INTO ProfileTypes VALUES ('Group');

-- List of countries
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('AF','AFG','004','Afghanistan');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('AX','ALA','248','�land Islands');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('AL','ALB','008','Albania');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('DZ','DZA','012','Algeria');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('AS','ASM','016','American Samoa');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('AD','AND','020','Andorra');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('AO','AGO','024','Angola');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('AI','AIA','660','Anguilla');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('AQ','ATA','010','Antarctica');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('AG','ATG','028','Antigua and Barbuda');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('AR','ARG','032','Argentina');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('AM','ARM','051','Armenia');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('AW','ABW','533','Aruba');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('AU','AUS','036','Australia');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('AT','AUT','040','Austria');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('AZ','AZE','031','Azerbaijan');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('BS','BHS','044','Bahamas');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('BH','BHR','048','Bahrain');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('BD','BGD','050','Bangladesh');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('BB','BRB','052','Barbados');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('BY','BLR','112','Belarus');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('BE','BEL','056','Belgium');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('BZ','BLZ','084','Belize');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('BJ','BEN','204','Benin');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('BM','BMU','060','Bermuda');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('BT','BTN','064','Bhutan');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('BO','BOL','068','Bolivia');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('BA','BIH','070','Bosnia and Herzegovina');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('BW','BWA','072','Botswana');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('BV','BVT','074','Bouvet Island');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('BR','BRA','076','Brazil');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('IO','IOT','086','British Indian Ocean Territory ');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('BN','BRN','096','Brunei Darussalam');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('BG','BGR','100','Bulgaria');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('BF','BFA','854','Burkina Faso');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('BI','BDI','108','Burundi');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('KH','KHM','116','Cambodia');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('CM','CMR','120','Cameroon');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('CA','CAN','124','Canada');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('CV','CPV','132','Cape Verde');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('KY','CYM','136','Cayman Islands');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('CF','CAF','140','Central African Republic');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('TD','TCD','148','Chad');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('CL','CHL','152','Chile');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('CN','CHN','156','China');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('CX','CXR','162','Christmas Island');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('CC','CCK','166','Cocos (Keeling) Islands');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('CO','COL','170','Colombia');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('KM','COM','174','Comoros');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('CG','COG','178','Congo');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('CD','COD','180','Democratic Republic of the Congo');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('CK','COK','184','Cook Islands');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('CR','CRI','188','Costa Rica');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('CI','CIV','384','C�te d''Ivoire');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('HR','HRV','191','Croatia');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('CU','CUB','192','Cuba');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('CY','CYP','196','Cyprus');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('CZ','CZE','203','Czech Republic');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('DK','DNK','208','Denmark');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('DJ','DJI','262','Djibouti');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('DM','DMA','212','Dominica');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('DO','DOM','214','Dominican Republic');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('EC','ECU','218','Ecuador');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('EG','EGY','818','Egypt');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('SV','SLV','222','El Salvador');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('GQ','GNQ','226','Equatorial Guinea');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('ER','ERI','232','Eritrea');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('EE','EST','233','Estonia');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('ET','ETH','231','Ethiopia');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('FK','FLK','238','Falkland Islands');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('FO','FRO','234','Faroe Islands');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('FJ','FJI','242','Fiji');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('FI','FIN','246','Finland');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('FR','FRA','250','France');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('GF','GUF','254','French Guiana');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('PF','PYF','258','French Polynesia');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('TF','ATF','260','French Southern Territories');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('GA','GAB','266','Gabon');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('GM','GMB','270','Gambia');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('GE','GEO','268','Georgia');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('DE','DEU','276','Germany');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('GH','GHA','288','Ghana');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('GI','GIB','292','Gibraltar');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('GR','GRC','300','Greece');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('GL','GRL','304','Greenland');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('GD','GRD','308','Grenada');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('GP','GLP','312','Guadeloupe');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('GU','GUM','316','Guam');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('GT','GTM','320','Guatemala');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('GG','GGY','831','Guernsey');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('GN','GIN','324','Guinea');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('GW','GNB','624','Guinea-Bissau');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('GY','GUY','328','Guyana');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('HT','HTI','332','Haiti');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('HM','HMD','334','Heard Island and McDonald Islands');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('VA','VAT','336','Holy See (Vatican City State)');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('HN','HND','340','Honduras');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('HK','HKG','344','Hong Kong');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('HU','HUN','348','Hungary');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('IS','ISL','352','Iceland');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('IN','IND','356','India');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('ID','IDN','360','Indonesia');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('IR','IRN','364','Iran, Islamic Republic of');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('IQ','IRQ','368','Iraq');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('IE','IRL','372','Ireland');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('IM','IMN','833','Isle of Man');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('IL','ISR','376','Israel');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('IT','ITA','380','Italy');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('JM','JAM','388','Jamaica');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('JP','JPN','392','Japan');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('JE','JEY','832','Jersey');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('JO','JOR','400','Jordan');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('KZ','KAZ','398','Kazakhstan');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('KE','KEN','404','Kenya');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('KI','KIR','296','Kiribati');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('KP','PRK','408','Korea, Democratic People''s Republic of');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('KR','KOR','410','Korea, Republic of ');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('KW','KWT','414','Kuwait');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('KG','KGZ','417','Kyrgyzstan');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('LA','LAO','418','Lao People''s Democratic Republic');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('LV','LVA','428','Latvia');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('LB','LBN','422','Lebanon');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('LS','LSO','426','Lesotho');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('LR','LBR','430','Liberia');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('LY','LBY','434','Libyan Arab Jamahiriya');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('LI','LIE','438','Liechtenstein');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('LT','LTU','440','Lithuania');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('LU','LUX','442','Luxembourg');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('MO','MAC','446','Macao');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('MK','MKD','807','Macedonia, the former Yugoslav Republic of');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('MG','MDG','450','Madagascar');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('MW','MWI','454','Malawi');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('MY','MYS','458','Malaysia');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('MV','MDV','462','Maldives');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('ML','MLI','466','Mali');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('MT','MLT','470','Malta');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('MH','MHL','584','Marshall Islands');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('MQ','MTQ','474','Martinique');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('MR','MRT','478','Mauritania');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('MU','MUS','480','Mauritius');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('YT','MYT','175','Mayotte');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('MX','MEX','484','Mexico');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('FM','FSM','583','Micronesia, Federated States of');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('MD','MDA','498','Moldova, Republic of ');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('MC','MCO','492','Monaco');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('MN','MNG','496','Mongolia');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('ME','MNE','499','Montenegro');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('MS','MSR','500','Montserrat');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('MA','MAR','504','Morocco');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('MZ','MOZ','508','Mozambique');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('MM','MMR','104','Myanmar');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('NA','NAM','516','Namibia');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('NR','NRU','520','Nauru');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('NP','NPL','524','Nepal');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('NL','NLD','528','Netherlands');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('AN','ANT','530','Netherlands Antilles');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('NC','NCL','540','New Caledonia');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('NZ','NZL','554','New Zealand');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('NI','NIC','558','Nicaragua');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('NE','NER','562','Niger');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('NG','NGA','566','Nigeria');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('NU','NIU','570','Niue');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('NF','NFK','574','Norfolk Island');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('MP','MNP','580','Northern Mariana Islands');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('NO','NOR','578','Norway');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('OM','OMN','512','Oman');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('PK','PAK','586','Pakistan');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('PW','PLW','585','Palau');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('PS','PSE','275','Palestinian Territory, Occupied');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('PA','PAN','591','Panama');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('PG','PNG','598','Papua New Guinea');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('PY','PRY','600','Paraguay');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('PE','PER','604','Peru');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('PH','PHL','608','Philippines');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('PN','PCN','612','Pitcairn ');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('PL','POL','616','Poland');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('PT','PRT','620','Portugal');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('PR','PRI','630','Puerto Rico');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('QA','QAT','634','Qatar');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('RE','REU','638','R�union');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('RO','ROU','642','Romania');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('RU','RUS','643','Russia');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('RW','RWA','646','Rwanda');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('SH','SHN','654','Saint Helena');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('KN','KNA','659','Saint Kitts and Nevis');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('LC','LCA','662','Saint Lucia');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('PM','SPM','666','Saint Pierre and Miquelon');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('VC','VCT','670','Saint Vincent and the Grenadines');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('WS','WSM','882','Samoa');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('SM','SMR','674','San Marino');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('ST','STP','678','Sao Tome and Principe');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('SA','SAU','682','Saudi Arabia');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('SN','SEN','686','Senegal');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('RS','SRB','688','Serbia');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('SC','SYC','690','Seychelles');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('SL','SLE','694','Sierra Leone');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('SG','SGP','702','Singapore');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('SK','SVK','703','Slovakia');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('SI','SVN','705','Slovenia');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('SB','SLB','090','Solomon Islands');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('SO','SOM','706','Somalia');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('ZA','ZAF','710','South Africa');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('GS','SGS','239','South Georgia and the South Sandwich Islands');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('ES','ESP','724','Spain');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('LK','LKA','144','Sri Lanka');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('SD','SDN','736','Sudan');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('SR','SUR','740','Suriname');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('SJ','SJM','744','Svalbard and Jan Mayen');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('SZ','SWZ','748','Swaziland');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('SE','SWE','752','Sweden');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('CH','CHE','756','Switzerland');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('SY','SYR','760','Syrian Arab Republic');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('TW','TWN','158','Taiwan ');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('TJ','TJK','762','Tajikistan');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('TZ','TZA','834','Tanzania, United Republic of');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('TH','THA','764','Thailand');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('TL','TLS','626','Timor-Leste');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('TG','TGO','768','Togo');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('TK','TKL','772','Tokelau');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('TO','TON','776','Tonga');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('TT','TTO','780','Trinidad and Tobago');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('TN','TUN','788','Tunisia');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('TR','TUR','792','Turkey');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('TM','TKM','795','Turkmenistan');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('TC','TCA','796','Turks and Caicos Islands');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('TV','TUV','798','Tuvalu');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('UG','UGA','800','Uganda');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('UA','UKR','804','Ukraine');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('AE','ARE','784','United Arab Emirates');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('GB','GBR','826','United Kingdom');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('US','USA','840','United States');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('UM','UMI','581','United States Minor Outlying Islands');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('UY','URY','858','Uruguay');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('UZ','UZB','860','Uzbekistan');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('VU','VUT','548','Vanuatu');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('VE','VEN','862','Venezuela');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('VN','VNM','704','Viet Nam ');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('VG','VGB','092','Virgin Islands, British');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('VI','VIR','850','Virgin Islands, U.S. ');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('WF','WLF','876','Wallis and Futuna');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('EH','ESH','732','Western Sahara');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('YE','YEM','887','Yemen');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('ZM','ZMB','894','Zambia');
INSERT INTO Countries (Alpha_2ISOCode,Alpha_3ISOCode,NumericISOCode,CountryName) VALUES ('ZW','ZWE','716','Zimbabwe');

-- States/Provinces that can be used in the US.
INSERT INTO US_StateProvinces VALUES ('AL','Alabama');
INSERT INTO US_StateProvinces VALUES ('AK','Alaska');
INSERT INTO US_StateProvinces VALUES ('AS','American Samoa');
INSERT INTO US_StateProvinces VALUES ('AZ','Arizona');
INSERT INTO US_StateProvinces VALUES ('AR','Arkansas');
INSERT INTO US_StateProvinces VALUES ('AA','Armed Forces Americas');
INSERT INTO US_StateProvinces VALUES ('AE','Armed Forces Europe');
INSERT INTO US_StateProvinces VALUES ('AP','Armed Forces Pacific');
INSERT INTO US_StateProvinces VALUES ('CA','California');
INSERT INTO US_StateProvinces VALUES ('CO','Colorado');
INSERT INTO US_StateProvinces VALUES ('CT','Connecticut');
INSERT INTO US_StateProvinces VALUES ('DE','Delaware');
INSERT INTO US_StateProvinces VALUES ('DC','District of Columbia');
INSERT INTO US_StateProvinces VALUES ('FM','Federated Micronesia');
INSERT INTO US_StateProvinces VALUES ('FL','Florida');
INSERT INTO US_StateProvinces VALUES ('GA','Georgia');
INSERT INTO US_StateProvinces VALUES ('GU','Guam');
INSERT INTO US_StateProvinces VALUES ('HI','Hawaii');
INSERT INTO US_StateProvinces VALUES ('NH','New Hampshire');
INSERT INTO US_StateProvinces VALUES ('ID','Idaho');
INSERT INTO US_StateProvinces VALUES ('IL','Illinois');
INSERT INTO US_StateProvinces VALUES ('IN','Indiana');
INSERT INTO US_StateProvinces VALUES ('IA','Iowa');
INSERT INTO US_StateProvinces VALUES ('KS','Kansas');
INSERT INTO US_StateProvinces VALUES ('KY','Kentucky');
INSERT INTO US_StateProvinces VALUES ('LA','Louisiana');
INSERT INTO US_StateProvinces VALUES ('ME','Maine');
INSERT INTO US_StateProvinces VALUES ('MH','Marshall Islands');
INSERT INTO US_StateProvinces VALUES ('MD','Maryland');
INSERT INTO US_StateProvinces VALUES ('MA','Massachusetts');
INSERT INTO US_StateProvinces VALUES ('MI','Michigan');
INSERT INTO US_StateProvinces VALUES ('MN','Minnesota');
INSERT INTO US_StateProvinces VALUES ('MS','Mississippi');
INSERT INTO US_StateProvinces VALUES ('MO','Missouri');
INSERT INTO US_StateProvinces VALUES ('MT','Montana');
INSERT INTO US_StateProvinces VALUES ('MP','N. Mariana Islands');
INSERT INTO US_StateProvinces VALUES ('NE','Nebraska');
INSERT INTO US_StateProvinces VALUES ('NV','Nevada');
INSERT INTO US_StateProvinces VALUES ('NJ','New Jersey');
INSERT INTO US_StateProvinces VALUES ('NM','New Mexico');
INSERT INTO US_StateProvinces VALUES ('NY','New York');
INSERT INTO US_StateProvinces VALUES ('NC','North Carolina');
INSERT INTO US_StateProvinces VALUES ('ND','North Dakota');
INSERT INTO US_StateProvinces VALUES ('OH','Ohio');
INSERT INTO US_StateProvinces VALUES ('OK','Oklahoma');
INSERT INTO US_StateProvinces VALUES ('OR','Oregon');
INSERT INTO US_StateProvinces VALUES ('PW','Palau');
INSERT INTO US_StateProvinces VALUES ('PA','Pennsylvania');
INSERT INTO US_StateProvinces VALUES ('PR','Puerto Rico');
INSERT INTO US_StateProvinces VALUES ('RI','Rhode Island');
INSERT INTO US_StateProvinces VALUES ('SC','South Carolina');
INSERT INTO US_StateProvinces VALUES ('SD','South Dakota');
INSERT INTO US_StateProvinces VALUES ('TN','Tennessee');
INSERT INTO US_StateProvinces VALUES ('TX','Texas');
INSERT INTO US_StateProvinces VALUES ('VI','US Virgin Islands');
INSERT INTO US_StateProvinces VALUES ('UT','Utah');
INSERT INTO US_StateProvinces VALUES ('VT','Vermont');
INSERT INTO US_StateProvinces VALUES ('VA','Virginia');
INSERT INTO US_StateProvinces VALUES ('WA','Washington');
INSERT INTO US_StateProvinces VALUES ('WV','West Virginia');
INSERT INTO US_StateProvinces VALUES ('WI','Wisconsin');
INSERT INTO US_StateProvinces VALUES ('WY','Wyoming');

-- Looking for.
INSERT INTO LookingForOptions VALUES ('Friendship');
INSERT INTO LookingForOptions VALUES ('Dating');
INSERT INTO LookingForOptions VALUES ('A Relationship');
INSERT INTO LookingForOptions VALUES ('Random Play');
INSERT INTO LookingForOptions VALUES ('Whatever I can get');

-- SexualPreference (interested in)
INSERT INTO SexualPreferenceOptions VALUES ('Men');
INSERT INTO SexualPreferenceOptions VALUES ('Women');

-- Relationship types
INSERT INTO BiDirectionalRelationshipTypes VALUES ('Friends');

-- MIMETypes
INSERT INTO MIMETypes VALUES ('image/jpeg');
-- gif
-- png

-- Element Packages
INSERT INTO ElementPackages VALUES ('UserImage');
INSERT INTO ElementPackages VALUES ('UserProfileCube');

-- Element Types
INSERT INTO ElementTypes (ElementType,ElementPackagePath,ElementPackage) VALUES ('ProfileImageElement',  '', 'UserImage');
INSERT INTO ElementTypes (ElementType,ElementPackagePath,ElementPackage) VALUES ('UserProfileBlogsListCube',  '../cubes/B/Blog.gloo', 'UserProfileCube');
INSERT INTO ElementTypes (ElementType,ElementPackagePath,ElementPackage) VALUES ('UserProfileFriendsListCube',  '../cubes/F/Friends.gloo', 'UserProfileCube');
INSERT INTO ElementTypes (ElementType,ElementPackagePath,ElementPackage) VALUES ('UserProfileProfileManagerCube',  '../cubes/P/ProfileManager.gloo', 'UserProfileCube');

-- eGloo accounts
-- System Account
BEGIN;
INSERT INTO PageNames 
	VALUES ('egAppAdmin');
INSERT INTO Users (UserType, UserName, UserPasswordHash, PassPhraseQuestion, PassPhraseAnswer, BirthDate, NamePrefix, FirstName, MiddleName, LastName, NameSuffix, 
                   Gender, UserAssociationLevel, numberofinvites, active ) 
	VALUES ('Administrator', 'egAppAdmin', 'ff199eae2e188f250e4ee884331e17a2400ad590e6a5796cbe1a52840af712f2', 'Password Question?', 'system', '01/01/1901', NULL, 'eGloo', '','Admin', NULL, 'Male', 0, 300, true);
INSERT INTO Profiles (ProfileCreator,ProfileName,AdultProfile,ProfileType) 
	VALUES (currval('seq_Users_User_ID'), 'egAppAdmin', TRUE, 'Individual');
INSERT INTO IndividualProfiles 
	VALUES (currval('seq_Profiles_Profile_ID'), TRUE);

--INSERT INTO EmailAddresses (EmailAddress) 
--	VALUES ('@egloo.com');
--INSERT INTO UserEmailAddresses (User_ID,EmailAddress,UserMainEmailAddress) 
--	VALUES ((SELECT User_ID FROM Users WHERE UserName='egAppAdmin'), '@egloo.com', TRUE);
COMMIT;

CREATE TABLE ProfileLayout (
	Profile_ID															BIGINT NOT NULL,
	Element_ID															BIGINT NOT NULL,
	LayoutRow															SMALLINT NOT NULL,
	LayoutColumn														SMALLINT NOT NULL,
CONSTRAINT pk_ProfileLayout PRIMARY KEY (Profile_ID, LayoutRow, LayoutColumn),
CONSTRAINT fk_ProfileLayout_Profile_ID FOREIGN KEY (Profile_ID)
	REFERENCES Profiles(Profile_ID)
	MATCH FULL
	ON DELETE NO ACTION
	ON UPDATE CASCADE,
CONSTRAINT fk_ProfileLayout_Element_ID FOREIGN KEY (Element_ID)
	REFERENCES Elements(Element_ID)
	MATCH FULL
	ON DELETE NO ACTION
	ON UPDATE CASCADE
);


GRANT SELECT, INSERT, UPDATE, DELETE ON TABLE ProfileLayout TO WebServer;
-- Should the db make sure rows and columns don't overlap.
-- Should singleton stuff be taken into account here.
-- Fridge?
-- Can the row and column be null . . . ?
-- What other info should be stored. . . 
--Update the Elements table to add the RANK column
ALTER TABLE Elements ADD Rank BIGINT; -- Not null?, Default value?;

GRANT SELECT, INSERT, UPDATE, DELETE ON TABLE Elements TO WebServer;

--Create the Ranking table
CREATE TABLE ProfileElementRankings (
	Element_ID															BIGINT NOT NULL,
	Profile_ID															BIGINT NOT NULL,
	Ranking																BIGINT NOT NULL,
	DateRanked															TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
CONSTRAINT pk_ProfileElementRankings PRIMARY KEY (Element_ID, Profile_ID),
CONSTRAINT fk_ProfileElementRankings_Element_ID FOREIGN KEY (Element_ID)
	REFERENCES Elements(Element_ID)
	MATCH FULL
	ON DELETE CASCADE
	ON UPDATE CASCADE,
CONSTRAINT fk_ProfileElementRankings_Profile_ID FOREIGN KEY (Profile_ID)
	REFERENCES Profiles(Profile_ID)
	MATCH FULL
	ON DELETE NO ACTION
	ON UPDATE CASCADE
);

GRANT SELECT, INSERT, UPDATE, DELETE ON TABLE ProfileElementRankings TO WebServer;

-- Function: rankelement(IN input_profile_id BIGINT, IN input_element_id BIGINT, IN input_rank BIGINT, OUT output_successful bool, OUT output_datecreated text)
-- Rank an element.

-- DROP FUNCTION rankelement(IN input_profile_id BIGINT, IN input_element_id BIGINT, IN input_rank BIGINT, OUT output_successful bool, OUT output_dateranked TIMESTAMP);

CREATE OR REPLACE FUNCTION rankelement(IN input_profile_id BIGINT, IN input_element_id BIGINT, IN input_rank BIGINT, OUT output_successful bool, OUT output_dateranked TIMESTAMP) AS
$BODY$
	DECLARE
	
	BEGIN
                --Update the rank if given by an existing user, otherwise insert a new ranking
		UPDATE profileelementrankings SET ranking = input_rank WHERE (profile_id=input_profile_id) AND (element_id = input_element_id);
		IF NOT FOUND THEN
			INSERT INTO profileelementrankings (profile_id, element_id, ranking) VALUES (input_profile_id, input_element_id, input_rank);
		END IF;
		

		--Retrieve the date the ranking was created.
		SELECT INTO 
		output_dateranked
		dateranked
		FROM profileelementrankings
		WHERE element_id = input_element_id;
		
		output_successful:=FOUND;

		--Update the rank for the element
		IF output_successful THEN
			UPDATE elements SET 
			rank = (SELECT AVG(profileelementrankings.ranking) FROM profileelementrankings WHERE profileelementrankings.element_id = input_element_id)
			WHERE element_id = input_element_id;
		END IF;

	END;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE;
ALTER FUNCTION rankelement(IN input_profile_id BIGINT, IN input_element_id BIGINT, IN input_rank BIGINT, OUT output_successful bool, OUT output_dateranked TIMESTAMP) OWNER TO postgres;


-- Function: getElementRanking(IN input_element_id BIGINT, OUT output_rank BIGINT, OUT output_successful bool)

-- DROP FUNCTION getElementRanking(IN input_element_id BIGINT, OUT output_rank BIGINT, OUT output_successful bool);

CREATE OR REPLACE FUNCTION getElementRanking(IN input_element_id BIGINT, OUT output_rank BIGINT, OUT output_successful bool) AS
$BODY$
	DECLARE
	
	BEGIN
                --Retrieve the ranking for a specific element
		SELECT INTO 
		output_rank 
		rank
		FROM elements 
		WHERE element_id = input_element_id;

		output_successful:=FOUND;
	END;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE;
ALTER FUNCTION getElementRanking(IN input_element_id BIGINT, OUT output_rank BIGINT, OUT output_successful bool) OWNER TO postgres;


-- Function: getProfileElementRanking(IN input_element_id BIGINT, IN input_profile_id BIGINT, OUT output_rank BIGINT, OUT output_successful bool)
-- To return the rank of a single element given by a specified profile.

-- DROP FUNCTION getProfileElementRanking(IN input_element_id BIGINT, IN input_profile_id BIGINT, OUT output_rank BIGINT, OUT output_dateranked TIMESTAMP, OUT output_successful bool);

CREATE OR REPLACE FUNCTION getProfileElementRanking(IN input_element_id BIGINT, IN input_profile_id BIGINT, OUT output_rank BIGINT, OUT output_dateranked TIMESTAMP, OUT output_successful bool) AS
$BODY$
	DECLARE
	
	BEGIN
                --Update the rank if given by an existing user, otherwise insert a new ranking
		SELECT INTO 
		output_rank, output_dateranked
		ranking, dateranked
		FROM profileelementrankings
		WHERE element_id = input_element_id AND profile_id = input_profile_id;

		output_successful:=FOUND;
	
	END;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE;
ALTER FUNCTION getProfileElementRanking(IN input_element_id BIGINT, IN input_profile_id BIGINT, OUT output_rank BIGINT, OUT output_dateranked TIMESTAMP, OUT output_successful bool) OWNER TO postgres;


-- Modify the getElementInstance method to include the rank and the date it was created in the return statements

DROP FUNCTION getelementinstance(IN input_element_id int8, OUT output_elementtype_id int8, OUT output_creator_id int8, OUT output_elementpackagepath text);

CREATE OR REPLACE FUNCTION getElementInstance(IN input_Element_ID BIGINT, IN input_Profile_ID BIGINT, OUT output_ElementType_ID BIGINT, OUT output_Creator_ID BIGINT, OUT output_ElementPackagePath TEXT, OUT output_datecreated TIMESTAMP, OUT output_ElementRank BIGINT, OUT output_ProfileElementRank BIGINT, OUT output_DateProfileRanked TIMESTAMP) AS $getElementInstance$
	DECLARE
	
	BEGIN
		-- There is going to have to be a lot of these . . . based on each ElementType possibly.
		-- Need to spit back more information
		-- ElementType
	
		SELECT INTO
		output_ElementType_ID, output_Creator_ID, output_ElementPackagePath, output_datecreated, output_ElementRank
		Elements.ElementType_ID, Creator_ID, ElementPackagePath, DateCreated, rank
		FROM Elements INNER JOIN ElementTypes ON Elements.ElementType_ID=ElementTypes.ElementType_ID
		WHERE Element_ID=input_Element_ID;

		SELECT INTO
		output_ProfileElementRank, output_DateProfileRanked
		output_rank, output_dateranked FROM
		getProfileElementRanking( input_Element_ID, input_Profile_ID );
	END;
$getElementInstance$ LANGUAGE 'plpgsql';
ALTER FUNCTION getElementInstance(IN input_Element_ID BIGINT, IN input_Profile_ID BIGINT, OUT output_ElementType_ID BIGINT, OUT output_Creator_ID BIGINT, OUT output_ElementPackagePath TEXT, OUT output_datecreated TIMESTAMP, OUT output_ElementRank BIGINT, OUT output_ProfileElementRank BIGINT, OUT output_DateProfileRanked TIMESTAMP) OWNER TO postgres;

ALTER TABLE elementtypes ADD Singleton BOOLEAN DEFAULT TRUE; -- Not null?, Default value?;
--DROP FUNCTION createNewCubeInstance(IN input_elementtype_id BIGINT, IN input_creator_id BIGINT, OUT output_element_id BIGINT, OUT output_successful bool, OUT output_elementpackagepath TEXT, OUT output_datecreated TIMESTAMP);

CREATE OR REPLACE FUNCTION createNewCubeInstance(IN input_elementtype_id BIGINT, IN input_creator_id BIGINT, OUT output_element_id BIGINT, OUT output_successful bool, OUT output_elementpackagepath TEXT, OUT output_datecreated TIMESTAMP, OUT output_ElementRank BIGINT,  OUT output_profileelementrank BIGINT, OUT output_dateprofileranked "timestamp") AS $cubeSingletonExists$
	DECLARE
		singletoncreated boolean;
	BEGIN

	singletoncreated:=FALSE;

	-- Is this type NOT a Singleton OR does a Singleton of this Type NOT exist for the Profile?
	IF ((SELECT Singleton FROM elementtypes WHERE elementtype_id=input_elementtype_id ) IS FALSE ) 
	 OR ((SELECT COUNT(element_id) FROM elements WHERE elementtype_id=input_elementtype_id AND creator_id=input_creator_id)=0) THEN
	
		--Create a new element
		SELECT INTO 
		output_successful, output_element_id
		createnewelement.output_successful, createnewelement.output_element_id
		FROM createnewelement(input_creator_id, input_elementtype_id);

		singletoncreated:=TRUE;
	
	end if;

	--If the element was not already created, then simply retrieve the element_id
	IF NOT singletoncreated THEN
		SELECT INTO 
		output_element_id 
		elements.element_id
		FROM elements 
		WHERE elements.elementtype_id = input_elementtype_id 
		      AND elements.creator_id = input_creator_id;
	END IF;

	-- Retrieve the rest of the element information
	SELECT INTO 
	output_ElementPackagePath, output_datecreated, output_ElementRank, output_profileelementrank, output_dateprofileranked
	getelementinstance.output_ElementPackagePath, getelementinstance.output_datecreated, getelementinstance.output_ElementRank, getelementinstance.output_profileelementrank, getelementinstance.output_dateprofileranked
	FROM getelementinstance(output_element_id, input_creator_id);
	
	output_successful:=FOUND;

	END;

$cubeSingletonExists$ LANGUAGE 'plpgsql';

-- Relationships --
DROP TABLE UniDirectionalRelationships;
DROP TABLE UniDirectionalRelationshipTypes;
DROP TABLE BiDirectionalRelationships;
DROP TABLE BiDirectionalRelationshipTypes;
DROP TABLE Relationships CASCADE;
DROP SEQUENCE seq_Relationshps_Relationship_ID;

CREATE TABLE RelationshipTypes (
	RelationshipType													VARCHAR(50) NOT NULL,
CONSTRAINT pk_RelationshipTypes PRIMARY KEY (RelationshipType)
);

CREATE TABLE Relationships (
	AccepterProfile_ID													BIGINT NOT NULL,
	RequesterProfile_ID													BIGINT NOT NULL,
	Accepted															BOOLEAN DEFAULT FALSE NOT NULL,
	DateRequested														TIMESTAMP NOT NULL DEFAULT now(),
	DateAccepted														TIMESTAMP,
	RelationshipType													VARCHAR(50) NOT NULL,
CONSTRAINT pk_Relationships PRIMARY KEY (AccepterProfile_ID,RequesterProfile_ID,RelationshipType),
CONSTRAINT fk_Relationships_AccepterProfile_ID FOREIGN KEY (AccepterProfile_ID)
	REFERENCES Profiles(Profile_ID)
	MATCH FULL
	ON UPDATE CASCADE
	ON DELETE NO ACTION,
CONSTRAINT fk_Relationships_RequesterProfile_ID FOREIGN KEY (RequesterProfile_ID)
	REFERENCES Profiles(Profile_ID)
	MATCH FULL
	ON UPDATE CASCADE
	ON DELETE NO ACTION,
CONSTRAINT fk_Relationships_RelationshipTypes FOREIGN KEY (RelationshipType)
	REFERENCES RelationshipTypes(RelationshipType)
	MATCH FULL
	ON UPDATE CASCADE
	ON DELETE NO ACTION
);

GRANT SELECT, INSERT, UPDATE, DELETE ON TABLE relationships TO WebServer;

INSERT INTO RelationshipTypes VALUES ('Friends');

-- Notes:
-- Needs to check to make sure that there are not duplicates.  A relationship of the same type with the requestor and the accepter reversed. 
-- This check should probably be done before requesting relationships or when accepting relationships, depends on the metadata we want to save.

-- A normalization question is whether or not relationship requests should be seperated from the relationships themselves.
-- Question: Which is faster: search twice (accepter->requestor) or search through table that is twice as long (two rows for each relationship).--History should be available but not needed.  Set that all up.


-- Relationship Functions --
--Request a relationship - Returns whether it worked properly.
CREATE OR REPLACE FUNCTION requestRelationship(IN input_RequesterProfile_ID BIGINT, IN input_AccepterProfile_ID BIGINT, IN input_RelationshipType VARCHAR, OUT output_Successful BOOLEAN) AS $requestRelationship$
	DECLARE
	
	BEGIN
	
		output_Successful:=TRUE;
	
		INSERT INTO relationships(AccepterProfile_ID, RequesterProfile_ID, RelationshipType) VALUES (input_RequesterProfile_ID, input_AccepterProfile_ID, input_RelationshipType);

		-- If the row was not inserted, then set output_successful to false
		IF NOT FOUND THEN
    			output_Successful:=FALSE;
		END IF;

		--Instead of crashing, catch the exception and return false for output_successful
		EXCEPTION
		WHEN unique_violation THEN
			output_Successful:=FALSE;
			
	END;
$requestRelationship$ LANGUAGE 'plpgsql';

--Accept a relationship - Returns TRUE if successful, FALSE otherwise (In theory, this would only happen if the relationship didn't exist)
--Also sets the date of when the relationship was accepted.

--!!To think about: Do we want to UPDATE when a relationship is declined, or remove it from the table?
--!!History of relationships
CREATE OR REPLACE FUNCTION actOnRelationship(IN input_RequesterProfile_ID BIGINT, IN input_AccepterProfile_ID BIGINT, IN input_RelationshipType VARCHAR, IN input_accept_relationship BOOLEAN, OUT output_successful BOOLEAN) AS $acceptRelationship$
	DECLARE
	
	BEGIN

        IF input_accept_relationship THEN 
		UPDATE relationships SET accepted = input_accept_relationship, dateaccepted = now()
		WHERE accepterprofile_id=input_AccepterProfile_ID AND
		  requesterprofile_id=input_RequesterProfile_ID AND
		  relationshiptype = input_RelationshipType;
        ELSE
                DELETE FROM relationships
                WHERE accepterprofile_id=input_AccepterProfile_ID AND
                  requesterprofile_id=input_RequesterProfile_ID AND
                  relationshiptype = input_RelationshipType;
        END IF;
		  
	output_successful:=FOUND;
	
	END;
$acceptRelationship$ LANGUAGE 'plpgsql';

-- View to return profile_id, profilename, relationship type and the date the relationship request was created
CREATE or replace VIEW profilerelationshiprequests AS (
      SELECT profiles.profile_id, profiles.profilename, relationships.relationshiptype, relationships.daterequested FROM profiles, relationships 
      WHERE relationships.accepted = FALSE AND profiles.profile_id = relationships.requesterprofile_id
);
GRANT SELECT, INSERT, UPDATE, DELETE ON profilerelationshiprequests TO WebServer;

-- Return all requests that the given profile has yet to act upon
CREATE OR REPLACE FUNCTION getProfileRelationshipRequests(IN input_Profile_ID BIGINT) RETURNS SETOF profilerelationshiprequests AS $getProfileRelationshipRequests$
	DECLARE
            r_return profilerelationshiprequests;
	BEGIN
	
	FOR r_return IN SELECT profiles.profile_id, profiles.profilename, relationships.relationshiptype, relationships.daterequested FROM profiles, relationships
	WHERE 
	   relationships.accepterprofile_id = input_Profile_ID 
	   AND relationships.requesterprofile_id = profiles.profile_id 
           AND relationships.accepted = false
	LOOP
		RETURN NEXT r_return;
	END LOOP;
	
	END;
$getProfileRelationshipRequests$ LANGUAGE 'plpgsql';

-- Return the requester/profile_id, their name, and the relationship type.
--drop FUNCTION getProfileRelationships(IN input_Profile_ID BIGINT);
--DROP VIEW profilerelationships CASCADE;
CREATE OR REPLACE VIEW profilerelationships AS (
      SELECT profiles.profile_id, profiles.profilename, relationshiptypes.relationshiptype FROM profiles, relationshiptypes
);
GRANT SELECT, INSERT, UPDATE, DELETE ON profilerelationships TO WebServer;

-- Function to return all accepted relationships for a given profile
CREATE OR REPLACE FUNCTION getProfileRelationships(IN input_Profile_ID BIGINT) RETURNS SETOF profilerelationships AS $getProfileRelationships$
        DECLARE
                r_return profilerelationships;
	BEGIN

	FOR r_return IN SELECT * FROM profilerelationships, relationships 
	WHERE 
           ((relationships.requesterprofile_id = input_Profile_ID 
           AND profilerelationships.profile_id = relationships.accepterprofile_id) 
                OR 
	   (relationships.accepterprofile_id = input_Profile_ID 
           AND profilerelationships.profile_id = relationships.requesterprofile_id))
           AND (relationships.accepted = TRUE)
	LOOP
            RETURN NEXT r_return;
	END LOOP;
	
	RETURN;
	
	END;
$getProfileRelationships$ LANGUAGE 'plpgsql';

--End an existing relationship - Returns whether it worked properly.
CREATE OR REPLACE FUNCTION endRelationship(IN input_RequesterProfile_ID BIGINT, IN input_AccepterProfile_ID BIGINT, IN input_RelationshipType VARCHAR, OUT output_Successful BOOLEAN) AS $endRelationship$
	DECLARE
	
	BEGIN

	DELETE FROM relationships 
	WHERE accepterprofile_id=input_AccepterProfile_ID AND
		  requesterprofile_id=input_RequesterProfile_ID AND
		  relationshiptype = input_RelationshipType;
		  
	output_Successful:=FOUND;

	END;
$endRelationship$ LANGUAGE 'plpgsql';

--Relationship types between two profile ids - Returns a list.
CREATE OR REPLACE FUNCTION getProfileRelationshipTypes(IN input_RequesterProfile_ID BIGINT, IN input_AccepterProfile_ID BIGINT) RETURNS SETOF relationshiptypes AS $getProfileRelationshipTypes$
	DECLARE
			r_return relationshiptypes;
	BEGIN

	FOR r_return IN SELECT relationshiptype FROM relationships 
	WHERE accepterprofile_id = input_AccepterProfile_ID AND requesterprofile_id = input_RequesterProfile_ID LOOP
		RETURN NEXT r_return;
	END LOOP;
	
	RETURN;
	
	END;
$getProfileRelationshipTypes$ LANGUAGE 'plpgsql';

--Trigger functions--
-- Trigger function to check if the relationship requested already exists and is valid (one may not request a relationship with themselves)
CREATE OR REPLACE FUNCTION check_relationship_request () RETURNS TRIGGER AS $check_relationship$ 
  DECLARE

    -- Declare a variable to hold the customer ID.
    reqprofileid BIGINT;
    accprofileid BIGINT;
    datecreated TEXT;
    
  BEGIN
 
    -- Set the requester and accepter profile IDs to the declared variables, mainly for improved readability 
    reqprofileid := NEW.requesterprofile_id;
    accprofileid := NEW.accepterprofile_id;

    -- Check if the relationship exists in the table in any way 
    SELECT INTO datecreated daterequested  FROM relationships 
    WHERE 
    (reqprofileid = relationships.requesterprofile_id AND accprofileid = relationships.accepterprofile_id) OR
    (reqprofileid = relationships.accepterprofile_id AND accprofileid = relationships.requesterprofile_id);

    -- If the relationship already exists or is not valid (cannot request a relationship with self) then return NULL
    IF FOUND OR (reqprofileid = accprofileid) THEN
      RETURN NULL; 
    END IF;

    -- If everything successfully went through the function, then return the row to be inserted 
    RETURN NEW;
  END;
$check_relationship$ LANGUAGE 'plpgsql';

-- Create the trigger function
CREATE TRIGGER check_relationships
                BEFORE INSERT
                ON relationships FOR EACH ROW
                EXECUTE PROCEDURE check_relationship_request();

--Return the profile name for a given profile ID
CREATE OR REPLACE FUNCTION getProfileName(IN input_profile_ID BIGINT, OUT output_profile_name TEXT) AS $getProfileName$
	DECLARE
	
	BEGIN

	SELECT INTO output_profile_name p.profilename FROM profiles p
	WHERE p.profile_id=input_profile_ID;
	
	END;
$getProfileName$ LANGUAGE 'plpgsql';--Add a column to be able to specify if an elementtype is rankable.
ALTER TABLE elementtypes ADD Rankable BOOLEAN DEFAULT FALSE;

-- Function: rankelement(IN input_profile_id BIGINT, IN input_element_id BIGINT, IN input_rank BIGINT, OUT output_successful bool, OUT output_datecreated text)
-- Rank an element.
-- DROP FUNCTION rankelement(IN input_profile_id BIGINT, IN input_element_id BIGINT, IN input_rank BIGINT, OUT output_successful bool, OUT output_dateranked TIMESTAMP);

CREATE OR REPLACE FUNCTION rankelement(IN input_profile_id BIGINT, IN input_element_id BIGINT, IN input_rank BIGINT, OUT output_successful bool, OUT output_dateranked TIMESTAMP) AS
$BODY$
	DECLARE
		rankable boolean;
	BEGIN
        
        SELECT INTO rankable etype.rankable FROM ElementTypes etype INNER JOIN Elements e ON e.ElementType_ID=etype.ElementType_ID 
        WHERE e.element_id = input_element_id;

		IF rankable THEN

	        --Update the rank if given by an existing user, otherwise insert a new ranking
			UPDATE profileelementrankings SET ranking = input_rank WHERE (profile_id=input_profile_id) AND (element_id = input_element_id);
			IF NOT FOUND THEN
				INSERT INTO profileelementrankings (profile_id, element_id, ranking) VALUES (input_profile_id, input_element_id, input_rank);
			END IF;
				
	
			--Retrieve the date the ranking was created.
			SELECT INTO 
			output_dateranked
			dateranked
			FROM profileelementrankings
			WHERE element_id = input_element_id;
			
			output_successful:=FOUND;
	
			--Update the rank for the element
			IF output_successful THEN
				UPDATE elements SET 
				rank = (SELECT AVG(profileelementrankings.ranking) FROM profileelementrankings WHERE profileelementrankings.element_id = input_element_id)
				WHERE element_id = input_element_id;
			END IF;
		
		ELSE 
			output_successful:=FALSE;
	        output_dateranked:=NULL;
		END IF;

	END;
$BODY$ LANGUAGE 'plpgsql' VOLATILE;
ALTER FUNCTION rankelement(IN input_profile_id BIGINT, IN input_element_id BIGINT, IN input_rank BIGINT, OUT output_successful bool, OUT output_dateranked TIMESTAMP) OWNER TO postgres;


-- Modify the getElementInstance method to include the rank and the date it was created in the return statements

DROP FUNCTION getelementinstance(IN input_Element_ID BIGINT, IN input_Profile_ID BIGINT, OUT output_ElementType_ID BIGINT, OUT output_Creator_ID BIGINT, OUT output_ElementPackagePath TEXT, OUT output_datecreated TIMESTAMP, OUT output_ElementRank BIGINT, OUT output_ProfileElementRank BIGINT, OUT output_DateProfileRanked TIMESTAMP);

CREATE OR REPLACE FUNCTION getElementInstance(IN input_Element_ID BIGINT, IN input_Profile_ID BIGINT, OUT output_ElementType_ID BIGINT, OUT output_Creator_ID BIGINT, OUT output_ElementPackagePath TEXT, OUT output_datecreated TIMESTAMP, OUT output_rankable BOOLEAN, OUT output_ElementRank BIGINT, OUT output_ProfileElementRank BIGINT, OUT output_DateProfileRanked TIMESTAMP) AS $getElementInstance$
	DECLARE
	
	BEGIN
	
		-- There is going to have to be a lot of these . . . based on each ElementType possibly.
		-- Need to spit back more information
		-- ElementType
	
		SELECT INTO
		output_ElementType_ID, output_Creator_ID, output_ElementPackagePath, output_datecreated, output_rankable, output_ElementRank
		e.ElementType_ID, Creator_ID, ElementPackagePath, DateCreated, etype.rankable, rank
		FROM Elements e INNER JOIN ElementTypes etype ON e.ElementType_ID=etype.ElementType_ID
		WHERE e.Element_ID=input_Element_ID;

		IF output_rankable THEN
			SELECT INTO
			output_ProfileElementRank, output_DateProfileRanked
			output_rank, output_dateranked FROM
			getProfileElementRanking( input_Element_ID, input_Profile_ID );
		ELSE
			output_ElementRank:=NULL;
			output_ProfileElementRank:=NULL;
			output_DateProfileRanked:=NULL;
		END IF;

	END;
$getElementInstance$ LANGUAGE 'plpgsql';
ALTER FUNCTION getElementInstance(IN input_Element_ID BIGINT, IN input_Profile_ID BIGINT, OUT output_ElementType_ID BIGINT, OUT output_Creator_ID BIGINT, OUT output_ElementPackagePath TEXT, OUT output_datecreated TIMESTAMP, OUT output_rankable BOOLEAN, OUT output_ElementRank BIGINT, OUT output_ProfileElementRank BIGINT, OUT output_DateProfileRanked TIMESTAMP) OWNER TO postgres;

-- Modify createnewcubeinstance to return output_rankable
DROP FUNCTION createnewcubeinstance(IN input_elementtype_id int8, IN input_creator_id int8, OUT output_element_id int8, OUT output_successful bool, OUT output_elementpackagepath text, OUT output_datecreated "timestamp", OUT output_elementrank int8, OUT output_profileelementrank int8, OUT output_dateprofileranked "timestamp");

CREATE OR REPLACE FUNCTION createnewcubeinstance(IN input_elementtype_id int8, IN input_creator_id int8, OUT output_element_id int8, OUT output_successful bool, OUT output_elementpackagepath text, OUT output_datecreated "timestamp", OUT output_rankable BOOLEAN, OUT output_elementrank int8, OUT output_profileelementrank int8, OUT output_dateprofileranked "timestamp") AS
$BODY$
	DECLARE
		singletoncreated boolean;
	BEGIN

	singletoncreated:=FALSE;

	-- Is this type NOT a Singleton OR does a Singleton of this Type NOT exist for the Profile?
	IF ((SELECT Singleton FROM elementtypes WHERE elementtype_id=input_elementtype_id ) IS FALSE ) 
	 OR ((SELECT COUNT(element_id) FROM elements WHERE elementtype_id=input_elementtype_id AND creator_id=input_creator_id)=0) THEN
	
		--Create a new element
		SELECT INTO 
		output_successful, output_element_id
		createnewelement.output_successful, createnewelement.output_element_id
		FROM createnewelement(input_creator_id, input_elementtype_id);

		singletoncreated:=TRUE;
	
	end if;

	--If the element was not already created, then simply retrieve the element_id
	IF NOT singletoncreated THEN
		SELECT INTO 
		output_element_id 
		elements.element_id
		FROM elements 
		WHERE elements.elementtype_id = input_elementtype_id 
		      AND elements.creator_id = input_creator_id;
	END IF;

	-- Retrieve the rest of the element information
	SELECT INTO 
	output_ElementPackagePath, output_datecreated, output_rankable, output_ElementRank, output_profileelementrank, output_dateprofileranked
	getelementinstance.output_ElementPackagePath, getelementinstance.output_datecreated, getelementinstance.output_rankable, getelementinstance.output_ElementRank, getelementinstance.output_profileelementrank, getelementinstance.output_dateprofileranked
	FROM getelementinstance(output_element_id, input_creator_id);
	
	output_successful:=FOUND;

	END;

$BODY$
  LANGUAGE 'plpgsql' VOLATILE;
ALTER FUNCTION createnewcubeinstance(IN input_elementtype_id int8, IN input_creator_id int8, OUT output_element_id int8, OUT output_successful bool, OUT output_elementpackagepath text, OUT output_datecreated "timestamp", OUT output_rankable BOOLEAN, OUT output_elementrank int8, OUT output_profileelementrank int8, OUT output_dateprofileranked "timestamp") OWNER TO postgres;

--Remove pagelayout column
ALTER TABLE profiles DROP profilepagelayout;

--Drop setprofilepagelayout procedure
DROP FUNCTION setprofilepagelayout(IN input_profile_id int8, IN input_profilepagelayout text, OUT output_successful bool);

--Drop getprofilepagelayout procedure
DROP FUNCTION getprofilepagelayout(IN input_profile_id int8, OUT output_profilepagelayout text);
-- Returns the newest blog entry.
CREATE OR REPLACE FUNCTION getLatestBlog(IN input_profile_id int8, OUT output_blogid int8, OUT output_dateblogcreated TIMESTAMP, OUT output_dateedited TIMESTAMP, OUT output_blogtitle text, OUT output_blogcontent text) AS
$BODY$
	DECLARE
		output_blogwriter int8;
	BEGIN

		SELECT INTO
		output_blogid, output_dateblogcreated
		b.blog_id, b.dateblogcreated
		FROM blogs b
		WHERE b.dateblogcreated=
		(SELECT MAX(b.dateblogcreated) FROM BLOGS b WHERE b.blogwriter=input_profile_id );

		SELECT INTO 
		output_BlogWriter, output_DateBlogCreated, output_DateEdited, output_BlogTitle, output_BlogContent
		BlogWriter, DateBlogCreated, DateEdited, BlogTitle, BlogContent
		FROM Blogs
			INNER JOIN BlogEntries ON Blogs.Blog_ID=BlogEntries.Blog_ID
		WHERE Blogs.Blog_ID=output_blogid 
			AND DateEdited=(SELECT MAX(DateEdited) FROM BlogEntries WHERE Blog_ID=output_blogid);
	END;

$BODY$
  LANGUAGE 'plpgsql' VOLATILE;
ALTER FUNCTION getLatestBlog(IN input_profile_id int8, OUT output_blogid int8, OUT output_dateblogcreated TIMESTAMP, OUT output_dateedited TIMESTAMP, OUT output_blogtitle text, OUT output_blogcontent text) OWNER TO WebServer;

--drop 	VIEW updatedblogs;
CREATE or REPLACE VIEW updatedblogs AS (
      SELECT b.blog_id, be.blogtitle, be.dateedited
      FROM blogs b INNER JOIN blogentries be ON b.blog_id=be.blog_id WHERE
		dateedited in (SELECT MAX(dateedited) FROM blogentries GROUP BY blog_id)
);
GRANT SELECT, INSERT, UPDATE, DELETE ON updatedblogs TO WebServer;

--drop VIEW blogsummary CASCADE;
CREATE or replace VIEW blogsummary AS (
      SELECT b.blog_id, b.dateblogcreated, ub.blogtitle
      FROM updatedblogs ub INNER JOIN blogs b ON ub.blog_id = b.blog_id
);
GRANT SELECT, INSERT, UPDATE, DELETE ON blogsummary TO WebServer;

CREATE OR REPLACE FUNCTION getBlogList(IN input_profile_id int8) RETURNS SETOF blogsummary AS
$BODY$
	DECLARE
            r_return blogsummary;
	BEGIN
	
	FOR r_return IN SELECT bs.blog_id, bs.dateblogcreated, bs.blogtitle
					FROM blogsummary bs INNER JOIN blogs b ON b.blog_id=bs.blog_id
	WHERE 
	   b.blogwriter = input_profile_id
       ORDER BY bs.dateblogcreated DESC
	LOOP
		RETURN NEXT r_return;
	END LOOP;
END;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE;
ALTER FUNCTION getBlogList(IN input_profile_id int8) OWNER TO WebServer;CREATE SEQUENCE seq_BlogComments_BlogComment_ID
    INCREMENT 		1
    MINVALUE 		-9223372036854775808
    MAXVALUE 		9223372036854775807;

GRANT SELECT, UPDATE ON seq_BlogComments_BlogComment_ID TO WebServer;

CREATE OR REPLACE FUNCTION getBlogCommentCount(IN input_blogcomment_id int8, OUT output_blogcomment_count int8 ) AS
$BODY$
	DECLARE
	
	BEGIN
		SELECT INTO 
		output_blogcomment_count 
		count(*)
		FROM blogcomments
		WHERE blogcommentparent = input_blogcomment_id and blogcommentparent != blogcomment_id;

	
	END;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE;
ALTER FUNCTION getBlogCommentCount(IN input_blogcomment_id int8,  OUT output_blogcomment_count int8) OWNER TO WebServer;
    
-- Comments are a universal thing, need to discuss making a single commenting system benefits
-- and disadvantages.  
-- With universal commenting we can have comments on comments similar to digg, 
-- but right now we are using BlogCommentParent instead.  If the comment is the parent it's ID
-- is the same as the parent entry.
-- Also will need to be re-adjusted when permissions are implemented.

CREATE TABLE BlogComments (
	BlogComment_ID BIGINT DEFAULT NEXTVAL('seq_BlogComments_BlogComment_ID') NOT NULL UNIQUE,
	Blog_ID BIGINT NOT NULL, -- Associated blog, figured regardless of a blog's edited status, comments should remain.
	BlogCommentWriter BIGINT NOT NULL, -- Profile _ID of the comment writer.	
	BlogCommentParent BIGINT NOT NULL,
	DateBlogCommentCreated TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
	BlogCommentContent VARCHAR(1000) NOT NULL, -- Room for 250 4 letter words.  Arbitray assignment 
			-- right now, maybe should be text and slimmed down once statistics on comment sizes are
			-- taken from beta testing.
CONSTRAINT pk_BlogComments PRIMARY KEY (BlogComment_ID),
CONSTRAINT fk_BlogComments_Blog_ID FOREIGN KEY (Blog_ID)
	REFERENCES Blogs(Blog_ID)
	MATCH FULL
	ON DELETE NO ACTION
	ON UPDATE CASCADE,
CONSTRAINT fk_BlogComments_BlogCommentWriter FOREIGN KEY (BlogCommentWriter)
	REFERENCES IndividualProfiles(Profile_ID)
	MATCH FULL
	ON DELETE NO ACTION
	ON UPDATE CASCADE,
CONSTRAINT fk_BlogComments_BlogCommentParent FOREIGN KEY (BlogCommentParent)
	REFERENCES BlogComments(BlogComment_ID)
	MATCH FULL
	ON DELETE NO ACTION
	ON UPDATE CASCADE
);
	
GRANT SELECT, INSERT, UPDATE, DELETE ON TABLE BlogComments TO WebServer;
	
-- Sample insert for parent number = blogcomment_id
--INSERT INTO BlogComments (Blog_ID, BlogCommentWriter, BlogCommentParent, BlogCommentContent) 
--	VALUES ((SELECT Blog_ID FROM Blogs LIMIT 1), (SELECT Profile_ID FROM IndividualProfiles LIMIT 1), currval('seq_BlogComments_BlogComment_ID'), 'test parent entry');

-- if the above insert doesn't work then we just need to remove the "not null" from BlogCommentParent

-- Create new blog comment.

--INSERT INTO blogcomments (blogcomment_id, blog_id, blogcommentwriter,blogcommentparent,dateblogcommentcreated,blogcommentcontent)
--VALUES (1,-9223372036854775808,-9223372036854775808,1, CURRENT_TIMESTAMP ,'testing');


CREATE OR REPLACE FUNCTION createBlogComment(IN input_blog_id int8, IN input_blogcommentwriter int8, IN input_blogcommentparent int8, 
IN input_blogcommentcontent text, OUT output_successful bool) AS
$BODY$
	DECLARE

	BEGIN

		IF input_blogcommentparent is null THEN	
		INSERT INTO BlogComments ( blog_id, blogcommentwriter,blogcommentparent,blogcommentcontent)
		VALUES (input_blog_id,input_blogcommentwriter,currval('seq_BlogComments_BlogComment_ID'),input_blogcommentcontent);

		ELSE
		INSERT INTO BlogComments ( blog_id, blogcommentwriter,blogcommentparent,blogcommentcontent)
		VALUES (input_blog_id,input_blogcommentwriter,input_blogcommentparent,input_blogcommentcontent);

		END IF;

		output_Successful:=FOUND;
	
	END;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE;
ALTER FUNCTION createBlogComment(IN input_blog_id int8, IN input_blogcommentwriter int8, IN input_blogcommentparent int8, 
IN input_blogcommentcontent text, OUT output_successful bool) OWNER TO WebServer;

--DELETE FROM blogcomments 
CREATE OR REPLACE FUNCTION deleteBlogComment(IN input_blogcomment_id int8, OUT output_successful bool) AS
$BODY$
	DECLARE
	
	BEGIN
	
		DELETE FROM blogcomments WHERE blogcomment_id = input_blogcomment_id ;

		output_Successful:=FOUND;
	
	END;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE;
ALTER FUNCTION deleteBlogComment(IN input_commentblog_id int8, OUT output_successful bool) OWNER TO WebServer;

--update blogcomments with a new blogcommentcontent 
CREATE OR REPLACE FUNCTION updateBlogComment(IN input_blogcomment_id int8, IN input_blogcommentcontent text, OUT output_successful bool) AS
$BODY$
	DECLARE
	
	BEGIN
	
		update BlogComments set blogcommentcontent = input_blogcommentcontent where blogcomment_id = input_blogcomment_id;

		output_Successful:=FOUND;
	
	END;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE;
ALTER FUNCTION updateBlogComment(IN input_blogcomment_id int8, IN input_blogcommentcontent text, OUT output_successful bool) OWNER TO WebServer;

CREATE OR REPLACE VIEW blogcommentssummary AS (
      SELECT getBlogCommentCount(blogcomment_id) as childcommentcount, b.blogcomment_id, b.blog_id, b.blogcommentwriter, b.blogcommentparent, b.dateblogcommentcreated, b.blogcommentcontent, a.profilename
		FROM profiles a, blogcomments b where a.profile_id = b.blogcommentwriter
);
GRANT SELECT, INSERT, UPDATE, DELETE ON blogcommentssummary TO WebServer;


CREATE OR REPLACE FUNCTION getRootBlogCommentList(IN input_blog_id int8) RETURNS SETOF blogcommentssummary AS
$BODY$
	DECLARE
			
           r_return blogcommentssummary;
	BEGIN
	
 FOR r_return IN SELECT c.childcommentcount, c.blogcomment_id, c.blog_id, c.blogcommentwriter, c.blogcommentparent, c.dateblogcommentcreated, c.blogcommentcontent, c.profilename
  from blogcommentssummary c where c.blog_id = input_blog_id and c.blogcomment_id = c.blogcommentparent order by c.blogcomment_id

	LOOP
		RETURN NEXT r_return;
	END LOOP;
END;
$BODY$
 LANGUAGE 'plpgsql' VOLATILE;
 ALTER FUNCTION getRootBlogCommentList(IN input_blog_id int8) OWNER TO WebServer;


CREATE OR REPLACE FUNCTION getBlogCommentList(IN input_blogcomment_id int8) RETURNS SETOF blogcommentssummary AS
$BODY$
	DECLARE
			
           r_return blogcommentssummary;
	BEGIN
	
 FOR r_return IN SELECT c.childcommentcount,c.blogcomment_id, c.blog_id, c.blogcommentwriter, c.blogcommentparent, c.dateblogcommentcreated, c.blogcommentcontent, c.profilename
  from blogcommentssummary c where c.blogcommentparent = input_blogcomment_id and c.blogcomment_id != c.blogcommentparent order by c.blogcomment_id

	LOOP
		RETURN NEXT r_return;
	END LOOP;
END;
$BODY$
 LANGUAGE 'plpgsql' VOLATILE;
 ALTER FUNCTION getBlogCommentList(IN input_blogcomment_id int8) OWNER TO WebServer;

--Get a comment
-- Returns the newest blog entry.
CREATE OR REPLACE FUNCTION getBlogComment(IN input_blogcomment_id int8, OUT output_blogcomment_id int8, OUT output_blog_id int8, OUT output_blogcommentwriter int8, OUT output_blogcommentparent int8, OUT output_dateblogcommentcreated timestamp, OUT output_blogcommentcontent text, OUT output_profilename text ) AS
$BODY$
	DECLARE
	BEGIN

		SELECT INTO
		output_blogcomment_id, output_blog_id, output_blogcommentwriter, output_blogcommentparent, output_dateblogcommentcreated, output_blogcommentcontent, output_profilename
		bc.blogcomment_id, bc.blog_id, bc.blogcommentwriter, bc.blogcommentparent, bc.dateblogcommentcreated, bc.blogcommentcontent, p.profilename
		FROM blogcomments bc, profiles p   
		WHERE bc.blogcomment_id = input_blogcomment_id and p.profile_id = bc.blogcommentwriter;

	END;

$BODY$
  LANGUAGE 'plpgsql' VOLATILE;
ALTER FUNCTION getBlogComment(IN input_profile_id int8, OUT output_blogid int8, OUT output_dateblogcreated TIMESTAMP, OUT output_dateedited TIMESTAMP, OUT output_blogtitle text, OUT output_blogcontent text) OWNER TO WebServer;





alter table sessions alter useragent type varchar(256);

--start 12'th db script


CREATE OR REPLACE VIEW blogProfiles AS (
  SELECT  b.blogwriter, b.blog_id, b.dateblogcreated, a.profilename
          FROM profiles a, blogs b where a.profile_id = b.blogwriter

);

GRANT SELECT, INSERT, UPDATE, DELETE ON blogProfiles TO WebServer;


CREATE OR REPLACE FUNCTION getRecentUpdateBlogProfiles(IN input_profile_count int8) RETURNS SETOF blogProfiles AS
$BODY$
	DECLARE
		  	
           r_return blogProfiles;
	BEGIN
	
 FOR r_return IN SELECT DISTINCT on (c.blogwriter) c.blogwriter, c.blog_id,  c.dateblogcreated, c.profilename
  from blogProfiles c order by c.blogwriter, c.blog_id desc limit input_profile_count

	LOOP
		RETURN NEXT r_return;
	END LOOP;
END;
$BODY$
 LANGUAGE 'plpgsql' VOLATILE;
 ALTER FUNCTION getRecentUpdateBlogProfiles(IN input_profile_count int8) OWNER TO WebServer;


CREATE OR REPLACE VIEW blogProfilesMostReply AS (

  SELECT  d.blog_id, count(d.blog_id) as counter, b.blogwriter,
          b.dateblogcreated, a.profilename
          FROM profiles a, blogs b, blogcomments d where a.profile_id = b.blogwriter and d.blog_id = b.blog_id 
          GROUP BY d.blog_id, b.blogwriter, b.dateblogcreated, a.profilename order by counter DESC

);

GRANT SELECT, INSERT, UPDATE, DELETE ON blogProfilesMostReply TO WebServer;


CREATE OR REPLACE FUNCTION getBlogProfilesMostReply(IN input_profile_count int8) RETURNS SETOF blogProfilesMostReply AS
$BODY$
	DECLARE
		  	
           r_return blogProfilesMostReply;
	BEGIN
	
    FOR r_return IN SELECT c.blog_id, c.counter, c.blogwriter, c.dateblogcreated, c.profilename
    from blogProfilesMostReply c limit input_profile_count

	LOOP
		RETURN NEXT r_return;
	END LOOP;
END;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE;
ALTER FUNCTION getblogProfilesMostReply(IN input_profile_count int8) OWNER TO WebServer;


CREATE OR REPLACE FUNCTION setUserInvitationConfirmation(IN input_referral_id TEXT, IN input_inviteduser_id BIGINT, IN input_confirmation_id TEXT, OUT output_successful bool ) AS
$BODY$
	DECLARE
	BEGIN

		UPDATE userinvitations
		SET inviteduser_id = input_inviteduser_id, confirmation_id = input_confirmation_id
		WHERE referral_id = input_referral_id;

		output_successful:=FOUND;
	END;

$BODY$
  LANGUAGE 'plpgsql' VOLATILE;
ALTER FUNCTION setUserInvitationConfirmation(IN input_referral_id TEXT, IN input_inviteduser_id BIGINT, IN input_confirmation_id TEXT, OUT output_successful bool) OWNER TO WebServer;


CREATE OR REPLACE FUNCTION activateUserAccount(IN input_confirmation_id TEXT, IN input_inviteduser_id BIGINT, OUT output_successful bool ) AS
$BODY$
	DECLARE
	BEGIN

		UPDATE users
		SET active = TRUE
		WHERE user_id in (SELECT inviteduser_id from userinvitations where confirmation_id = input_confirmation_id and inviteduser_id = input_inviteduser_id);

		output_successful:=FOUND;
	END;

$BODY$
  LANGUAGE 'plpgsql' VOLATILE;
ALTER FUNCTION activateUserAccount(IN input_confirmation_id TEXT, IN input_inviteduser_id BIGINT, OUT output_successful bool  ) OWNER TO WebServer;


-- If the confirmation code exists then confirmation_id is not unique
CREATE OR REPLACE FUNCTION isConfirmation_IDUnique (IN input_confirmation_id TEXT, OUT output_confirmationunique BOOLEAN) AS 
$BODY$
	DECLARE
	
	BEGIN
		PERFORM confirmation_id FROM UserInvitations WHERE confirmation_id=input_confirmation_id;
		
		IF FOUND THEN
			output_confirmationunique:=FALSE;
		ELSE
			output_confirmationunique:=TRUE;		
		END IF;	
	END;
$BODY$ 
   LANGUAGE 'plpgsql';
ALTER FUNCTION isConfirmation_IDUnique(IN input_confirmation_id TEXT, OUT output_confirmationunique boolean  ) OWNER TO WebServer;


CREATE TABLE PasswordReset (
	User_ID															BIGINT NOT NULL, -- User that resets the password.
	PasswordResetRef												VARCHAR(20) NOT NULL, -- Password Reset Reference Number.
CONSTRAINT pk_User_ID PRIMARY KEY (User_ID),
CONSTRAINT fk_User_ID FOREIGN KEY (User_ID)
	REFERENCES Users(User_ID)
	MATCH FULL
	ON DELETE NO ACTION
	ON UPDATE CASCADE
);

GRANT SELECT, UPDATE, INSERT, DELETE ON TABLE PasswordReset TO WebServer;

CREATE OR REPLACE FUNCTION setPasswordResetConfirmation(IN input_emailaddress TEXT, IN input_passwordresetref TEXT, OUT output_user_id BIGINT ) AS
$BODY$
	DECLARE
		
	BEGIN
	
		SELECT INTO 
		output_user_id
		user_id
		from useremailaddresses where emailaddress like input_emailaddress;

		IF output_user_id is NULL THEN
			output_user_id:=NULL;
		ELSE
			IF (select user_id from passwordreset where user_id = output_user_id) is not null THEN
				
				DELETE FROM passwordreset where user_id = output_user_id; 
			END IF;

			INSERT INTO passwordreset (user_id, passwordresetref)
       		VALUES (output_user_id, input_passwordresetref);
				
		END IF;
	END;

$BODY$
  LANGUAGE 'plpgsql' VOLATILE;
ALTER FUNCTION  setPasswordResetConfirmation(IN input_emailaddress TEXT, IN input_passwordresetref TEXT, OUT output_user_id BIGINT ) OWNER TO WebServer;



CREATE OR REPLACE FUNCTION updateForgottenPassword(IN input_user_id BIGINT, IN input_passwordresetref TEXT, IN input_newuserpasswordhash TEXT, OUT output_successful bool ) AS
$BODY$
	DECLARE
	BEGIN

		UPDATE users
		SET userpasswordhash = input_newuserpasswordhash
		WHERE user_id in (SELECT user_id from passwordReset where user_id = input_user_id and passwordresetref= input_passwordresetref);

		output_successful:=FOUND;
		
		DELETE FROM passwordReset where user_id = input_user_id and passwordresetref = input_passwordresetref;

	END;

$BODY$
  LANGUAGE 'plpgsql' VOLATILE;
ALTER FUNCTION updateForgottenPassword(IN input_user_id BIGINT, IN input_passwordresetref TEXT, IN input_newpassword TEXT, OUT output_successful bool  ) OWNER TO WebServer;






