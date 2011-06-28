/** Baseline OLTP File.
 * 
 * @author Matthew Brennan
 * @version 0.1
 * 
 */

-- Email address sequence, domain, table.
CREATE SEQUENCE email_addresses_ident_seq
	MINVALUE 0
	START WITH 0;

CREATE DOMAIN email_addresses_ident_dom AS BIGINT
	DEFAULT NEXTVAL('email_addresses_ident_seq');

CREATE DOMAIN email_address_dom AS VARCHAR(256)
	NOT NULL;
-- Make sure first portion of email is the right size and
-- posses the right characters
-- Make sure the domain portion of the email address
-- is made up of the proper characters and has a period
-- seperatiing the portions of the address.
/*	CONSTRAINT ck_email_address CHECK
		(
			VALUE ~ '^[a-f0-9.]'
		);*/

CREATE TABLE email_addresses (
	email_address_id	email_addresses_ident_dom,
	email_address	email_address_dom,
	created_timestamp	TIMESTAMPTZ DEFAULT current_timestamp NOT NULL,
	modified_timestamp	TIMESTAMPTZ DEFAULT current_timestamp NOT NULL,
	created_by	BIGINT DEFAULT 0 NOT NULL,
	modified_by	BIGINT DEFAULT 0 NOT NULL,
CONSTRAINT pk_email_addresses PRIMARY KEY (email_address_id)
CONSTRAINT u_email_addresses_email_address UNIQUE (email_address)
);

-- refactor user types . . . make users into groups/roles similar to what 
-- Postgres and Unix do.
-- Usertypes sequence, domain, table
-- Decide late how to deal with "people" and "groups" outside of users
CREATE SEQUENCE user_types_ident_seq
	MINVALUE 0
	START WITH 0;

CREATE DOMAIN user_types_ident_dom AS BIGINT
	DEFAULT NEXTVAL('user_types_ident_seq');

CREATE TABLE user_types (
	user_type_id	user_types_ident_dom,
	user_type_label	VARCHAR(32) NOT NULL,
	user_type_description	TEXT NOT NULL,
	created_timestamp	TIMESTAMPTZ DEFAULT current_timestamp NOT NULL,
	modified_timestamp	TIMESTAMPTZ DEFAULT current_timestamp NOT NULL,
	created_by	BIGINT DEFAULT 0 NOT NULL,
	modified_by	BIGINT DEFAULT 0 NOT NULL,
CONSTRAINT pk_user_types PRIMARY KEY (user_type_id)
);



CREATE DOMAIN md5_hash_dom AS VARCHAR(32)
	NOT NULL
	CONSTRAINT md5_hash CHECK 
		(
			CHAR_LENGTH(value)=32
			AND value ~ '[a-f0-9]{32,32}'
		);

-- email for login.
CREATE SEQUENCE users_ident_seq
	MINVALUE 0
	START WITH 0;

CREATE DOMAIN users_ident_dom AS BIGINT
	DEFAULT NEXTVAL('users_ident_seq');

-- Decide what encryption is being used as a feature.

CREATE TABLE users (
	user_id	users_ident_dom,
	email_address_id	BIGINT NOT NULL,
	user_password_salt	VARCHAR(64) NOT NULL,
	user_password_hash	md5_hash_dom NOT NULL,
	user_security_question_hash	md5_hash_dom NOT NULL,
	user_security_answer_hash	md5_hash_dom NOT NULL,
	created_timestamp	TIMESTAMPTZ DEFAULT current_timestamp NOT NULL,
	modified_timestamp	TIMESTAMPTZ DEFAULT current_timestamp NOT NULL,
	created_by	BIGINT DEFAULT 0 NOT NULL,
	modified_by	BIGINT DEFAULT 0 NOT NULL,
CONSTRAINT pk_users PRIMARY KEY (user_id),
);

-- Create the "Initialation User" as user 0
-- Make sure the user sequence skips 0. . . 
-- Foreign key references for all of the tables that came before users

-- username for login
