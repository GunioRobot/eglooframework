/** Baseline eGloo Configuration File.
 * 
 * @author Matthew Brennan
 * @version 0.1
 * 
 */

--

-- eGloo Modules
CREATE SEQUENCE egloo_config.egloo_modules_ident_seq
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

-- eGloo Features
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

-- eGloo Feature Configurations



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