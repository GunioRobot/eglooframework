/** Baseline OLTP File.
 * 
 * @author Matthew Brennan
 * @version 0.1
 * 
 */

-- eGloo Modules
CREATE SEQUENCE egloo_modules_ident_seq
	MINVALUE 0
	START WITH 0;

CREATE DOMAIN egloo_modules_ident_dom AS BIGINT
	DEFAULT NEXTVAL('egloo_modules_ident_seq');

CREATE TABLE egloo_modules (
	egloo_module_id	egloo_modules_ident_dom,
	egloo_module_name	VARCHAR(64) NOT NULL,
	egloo_module_version	INTEGER NOT NULL,
	egloo_module_release	INTEGER NOT NULL,
	created_timestamp	TIMESTAMPTZ DEFAULT current_timestamp NOT NULL,
	modified_timestamp	TIMESTAMPTZ DEFAULT current_timestamp NOT NULL,
	created_by	BIGINT DEFAULT 0 NOT NULL,
	modified_by	BIGINT DEFAULT 0 NOT NULL,
CONSTRAINT pk_egloo_modules PRIMARY KEY (egloo_module_id),
);

-- Feature Dependancies
CREATE SEQUENCE features_ident_seq
	MINVALUE 0
	START WITH 0;

CREATE DOMAIN features_ident_dom AS BIGINT
	DEFAULT NEXTVAL('features_ident_seq');

CREATE TABLE features (
	feature_id	features_ident_dom,
	feature_name	VARCHAR(64) NOT NULL,
	created_timestamp	TIMESTAMPTZ DEFAULT current_timestamp NOT NULL,
	modified_timestamp	TIMESTAMPTZ DEFAULT current_timestamp NOT NULL,
	created_by	BIGINT DEFAULT 0 NOT NULL,
	modified_by	BIGINT DEFAULT 0 NOT NULL,
CONSTRAINT pk_features PRIMARY KEY (feature_id),
);

CREATE SEQUENCE feature_object_type_ident_seq
	MINVALUE 0
	START WITH 0;

CREATE DOMAIN feature_object_type_ident_dom AS BIGINT
	DEFAULT NEXTVAL('feature_object_type_ident_seq');

CREATE TABLE feature_object_type (
	feature_object_type_id	feature_object_type_ident_dom,
	created_timestamp	TIMESTAMPTZ DEFAULT current_timestamp NOT NULL,
	modified_timestamp	TIMESTAMPTZ DEFAULT current_timestamp NOT NULL,
	created_by	BIGINT DEFAULT 0 NOT NULL,
	modified_by	BIGINT DEFAULT 0 NOT NULL,
CONSTRAINT pk_feature_object_type PRIMARY KEY (feature_object_type_id),
);	

CREATE SEQUENCE feature_objects_ident_seq
	MINVALUE 0
	START WITH 0;

CREATE DOMAIN feature_objects_ident_dom AS BIGINT
	DEFAULT NEXTVAL('feature_objects_ident_seq');

CREATE TABLE feature_objects (
	feature_object_id	feature_objects_ident_dom,
	created_timestamp	TIMESTAMPTZ DEFAULT current_timestamp NOT NULL,
	modified_timestamp	TIMESTAMPTZ DEFAULT current_timestamp NOT NULL,
	created_by	BIGINT DEFAULT 0 NOT NULL,
	modified_by	BIGINT DEFAULT 0 NOT NULL,
CONSTRAINT pk_feature_objects PRIMARY KEY (feature_object_id),
);




-- Email address sequence, domain, table.
CREATE SEQUENCE email_addresses_ident_seq
	MINVALUE 0
	START WITH 0;

CREATE DOMAIN email_addresses_ident_dom AS BIGINT
	DEFAULT NEXTVAL('email_addresses_ident_seq');

CREATE DOMAIN email_address_dom AS VARCHAR(256)
	NOT NULL;
-- Check to ensure the email field is not too long
-- Make sure the emial field is has the proper characters.
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
);

-- Usertypes sequence, domain, table
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

