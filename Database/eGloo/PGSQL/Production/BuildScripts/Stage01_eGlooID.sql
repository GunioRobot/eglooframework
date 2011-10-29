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
-- TODO Add in DNS information
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
--CREATE TABLE VerifyEmailAddress (
--	EmailAddress														BIGINT NOT NULL,
--	User_ID																BIGINT NOT NULL,
--	EmailVerificationHex												VARCHAR(10) NOT NULL UNIQUE, -- might need to be longer
--	EmailVerificationTimeStamp											TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
--	EmailVerificationValidTill											TIMESTAMP NOT NULL DEFAULT (CURRENT_TIMESTAMP + INTERVAL '3 hours'), -- Might want to use a trigger for this. . . to get proper timestamp.
--CONSTRAINT pk_VerifyEmailAddress PRIMARY KEY (EmailAddress,User_ID,EmailVerificationHex),
--CONSTRAINT fk_VerifyEmailAddress_EmailAddress FOREIGN KEY (EmailAddress)
--	REFERENCES EmailAddresses(EmailAddress)
--	MATCH FULL
--	ON DELETE NO ACTION
--	ON UPDATE CASCADE,
--CONSTRAINT fk_VerifyEmailAddress_User_ID FOREIGN KEY (User_ID)
--	REFERENCES Users(User_ID)
--	MATCH FULL
--	ON DELETE NO ACTION
--	ON UPDATE CASCADE
--);
