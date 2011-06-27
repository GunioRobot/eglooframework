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

CREATE SEQUENCE users_ident_seq
	MINVALUE 0
	START WITH 0;

CREATE DOMAIN users_ident_dom AS BIGINT
	DEFAULT NEXTVAL('users_ident_seq');

CREATE TABLE users (
	user_id	users_ident_dom,
	created_timestamp	TIMESTAMPTZ DEFAULT current_timestamp NOT NULL,
	modified_timestamp	TIMESTAMPTZ DEFAULT current_timestamp NOT NULL,
	created_by	BIGINT DEFAULT 0 NOT NULL,
	modified_by	BIGINT DEFAULT 0 NOT NULL,
CONSTRAINT pk_users PRIMARY KEY (user_id),
);


-- email for login.
-- username for login
