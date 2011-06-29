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
CONSTRAINT pk_email_addresses PRIMARY KEY (email_address_id),
CONSTRAINT u_email_addresses_email_address UNIQUE (email_address)
);

/*
CREATE SEQUENCE role_types_ident_seq
	MINVALUE 0
	START WITH 0;

CREATE DOMAIN role_types_ident_dom AS BIGINT
	DEFAULT NEXTVAL('role_types_ident_seq');

CREATE TABLE role_types (
	role_type_id	role_types_ident_dom,
	role_type_label	VARCHAR(32) NOT NULL,
	role_type_description	TEXT NOT NULL,
	created_timestamp	TIMESTAMPTZ DEFAULT current_timestamp NOT NULL,
	modified_timestamp	TIMESTAMPTZ DEFAULT current_timestamp NOT NULL,
	created_by	BIGINT DEFAULT 0 NOT NULL,
	modified_by	BIGINT DEFAULT 0 NOT NULL,
CONSTRAINT pk_role_types PRIMARY KEY (role_type_id)
);
*/

CREATE DOMAIN md5_hash_dom AS VARCHAR(32)
	NOT NULL
	CONSTRAINT md5_hash CHECK 
		(
			CHAR_LENGTH(value)=32
			AND value ~ '[a-f0-9]{32,32}'
		);

-- email for login.
CREATE SEQUENCE roles_ident_seq
	MINVALUE 0
	START WITH 0;

CREATE DOMAIN roles_ident_dom AS BIGINT
	DEFAULT NEXTVAL('roles_ident_seq');


-- refactor role types . . . make roles into groups/roles similar to what 
-- Postgres and Unix do.
-- Usertypes sequence, domain, table
-- Decide late how to deal with "people" and "groups" outside of roles

-- Decide what encryption is being used as a feature.
CREATE TABLE roles (
	role_id	roles_ident_dom,
	role_password_salt	VARCHAR(64) NOT NULL,
	role_password_hash	md5_hash_dom NOT NULL,
	is_user BOOLEAN NOT NULL,
	is_group BOOLEAN NOT NULL,
	created_timestamp	TIMESTAMPTZ DEFAULT current_timestamp NOT NULL,
	modified_timestamp	TIMESTAMPTZ DEFAULT current_timestamp NOT NULL,
	created_by	BIGINT DEFAULT 0 NOT NULL,
	modified_by	BIGINT DEFAULT 0 NOT NULL,
CONSTRAINT pk_roles PRIMARY KEY (role_id)
);

CREATE TABLE users (
	user_id	BIGINT NOT NULL,
	email_address_id	BIGINT NOT NULL,
	user_security_question	VARCHAR(256) NOT NULL,
	user_security_answer_hash	md5_hash_dom NOT NULL,
	created_timestamp	TIMESTAMPTZ DEFAULT current_timestamp NOT NULL,
	modified_timestamp	TIMESTAMPTZ DEFAULT current_timestamp NOT NULL,
	created_by	BIGINT DEFAULT 0 NOT NULL,
	modified_by	BIGINT DEFAULT 0 NOT NULL,
CONSTRAINT pk_users PRIMARY KEY (user_id),
CONSTRAINT fk_users_user_id FOREIGN KEY (user_id)
	REFERENCES roles(role_id)
	MATCH FULL
	ON DELETE NO ACTION
	ON UPDATE CASCADE,
CONSTRAINT u_users_email_address_id UNIQUE (email_address_id)
);

CREATE TABLE groups (
	group_id	BIGINT NOT NULL,
	created_timestamp	TIMESTAMPTZ DEFAULT current_timestamp NOT NULL,
	modified_timestamp	TIMESTAMPTZ DEFAULT current_timestamp NOT NULL,
	created_by	BIGINT DEFAULT 0 NOT NULL,
	modified_by	BIGINT DEFAULT 0 NOT NULL,
CONSTRAINT pk_groups PRIMARY KEY (group_id),
CONSTRAINT fk_groups_group_id FOREIGN KEY (group_id)
	REFERENCES roles(role_id)
	MATCH FULL
	ON DELETE NO ACTION
	ON UPDATE CASCADE
);

CREATE SEQUENCE group_members_ident_seq
	MINVALUE 0
	START WITH 0;

CREATE DOMAIN group_members_ident_dom AS BIGINT
	DEFAULT NEXTVAL('group_members_ident_seq');

CREATE TABLE group_members (
	group_member_id	group_members_ident_dom,
	group_id	BIGINT NOT NULL,
	member_user_id	BIGINT NOT NULL,
	created_timestamp	TIMESTAMPTZ DEFAULT current_timestamp NOT NULL,
	modified_timestamp	TIMESTAMPTZ DEFAULT current_timestamp NOT NULL,
	created_by	BIGINT DEFAULT 0 NOT NULL,
	modified_by	BIGINT DEFAULT 0 NOT NULL,
CONSTRAINT pk_group_members PRIMARY KEY (group_member_id),
CONSTRAINT fk_group_members_group_id FOREIGN KEY (group_id)
	REFERENCES group_id(group_id)
	MATCH FULL
	ON DELETE NO ACTION
	ON UPDATE CASCADE,
CONSTRAINT fk_group_members_member_user_id FOREIGN KEY (member_user_id)
	REFERENCES users(user_id)
	MATCH FULL
	ON DELETE NO ACTION
	ON UPDATE CASCADE
);






-- Create the "Initialation User" as role 0
-- Make sure the role sequence skips 0. . . 
-- Foreign key references for all of the tables that came before roles

-- rolename for login
