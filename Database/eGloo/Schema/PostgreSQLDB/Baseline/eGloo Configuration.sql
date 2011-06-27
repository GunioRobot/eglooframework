/** Baseline eGloo Configuration File.
 * 
 * @author Matthew Brennan
 * @version 0.1
 * 
 */

-- The eGloo Administrator Role which will be used for config by the software.
CREATE ROLE egloo_admins WITH
	NOSUPERUSER
	NOCREATEDB
	NOCREATEROLE
	NOLOGIN;

CREATE USER egloo_config IN GROUP egloo_admins;

-- eGloo Schema
CREATE SCHEMA egloo_config AUTHORIZATION egloo_config;

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
CONSTRAINT pk_egloo_modules PRIMARY KEY (egloo_module_id)
);

-- eGloo Features
CREATE SEQUENCE egloo_features_ident_seq
	MINVALUE 0
	START WITH 0;

CREATE DOMAIN egloo_features_ident_dom AS BIGINT
	DEFAULT NEXTVAL('features_ident_seq');

CREATE TABLE egloo_features (
	egloo_feature_id	features_ident_dom,
	egloo_feature_name	VARCHAR(64) NOT NULL,
	created_timestamp	TIMESTAMPTZ DEFAULT current_timestamp NOT NULL,
	modified_timestamp	TIMESTAMPTZ DEFAULT current_timestamp NOT NULL,
	created_by	BIGINT DEFAULT 0 NOT NULL,
	modified_by	BIGINT DEFAULT 0 NOT NULL,
CONSTRAINT pk_egloo_features PRIMARY KEY (egloo_feature_id)
);

-- eGloo Feature Configuration Questions
-- eGloo Feature Configurations
CREATE SEQUENCE egloo_feature_configurations_ident_seq
	MINVALUE 0
	START WITH 0;

CREATE DOMAIN egloo_feature_configurations_ident_dom AS BIGINT
	DEFAULT NEXTVAL('egloo_feature_configurations_ident_seq');

CREATE TABLE egloo_feature_configurations (
	egloo_feature_config_id	egloo_feature_configurations_ident_dom,
	egloo_feature_id	BIGINT NOT NULL,
	created_timestamp	TIMESTAMPTZ DEFAULT current_timestamp NOT NULL,
	modified_timestamp	TIMESTAMPTZ DEFAULT current_timestamp NOT NULL,
	created_by	BIGINT DEFAULT 0 NOT NULL,
	modified_by	BIGINT DEFAULT 0 NOT NULL,
CONSTRAINT pk_egloo_feature_configurations PRIMARY KEY (egloo_feature_config_id),
CONSTRAINT fk_egloo_feature_configurations_egloo_feature_id FOREIGN KEY (egloo_feature_id)
	REFERENCES egloo_feature_id(egloo_feature_id)
	MATCH FULL
	ON DELETE NO ACTION
	ON UPDATE CASCADE
);

CREATE SEQUENCE egloo_feature_config_dependancies_ident_seq
	MINVALUE 0
	START WITH 0;

CREATE DOMAIN egloo_feature_config_dependancies_ident_dom AS BIGINT
	DEFAULT NEXTVAL('egloo_feature_config_dependancies_ident_seq');

CREATE TABLE egloo_feature_config_dependancies (
	egloo_feature_config_dependancy_id	egloo_feature_config_dependancies_ident_dom,
	dependant_feature_config_id	BIGINT NOT NULL,
	egloo_feature_config_id	BIGINT NOT NULL,
	created_timestamp	TIMESTAMPTZ DEFAULT current_timestamp NOT NULL,
	modified_timestamp	TIMESTAMPTZ DEFAULT current_timestamp NOT NULL,
	created_by	BIGINT DEFAULT 0 NOT NULL,
	modified_by	BIGINT DEFAULT 0 NOT NULL,
CONSTRAINT pk_egloo_feature_config_dependancies PRIMARY KEY (egloo_feature_config_dependancy_id),
CONSTRAINT fk_egloo_feature_config_dependancies_dependant_feature_config_id FOREIGN KEY (dependant_feature_config_id)
	REFERENCES egloo_feature_configurations(egloo_feature_config_id)
	MATCH FULL
	ON DELETE NO ACTION
	ON UPDATE CASCADE,
CONSTRAINT fk_egloo_feature_config_dependancies_egloo_feature_config_id FOREIGN KEY (egloo_feature_config_id)
	REFERENCES egloo_feature_configurations(egloo_feature_config_id)
	MATCH FULL
	ON DELETE NO ACTION
	ON UPDATE CASCADE
CONSTRAINT ck_egloo_feature_config_dependancies_same_dependant CHECK
	(
		dependant_feature_config_id!=egloo_feature_config_id
	)
);



CREATE SEQUENCE egloo_module_feature_configs_ident_seq
	MINVALUE 0
	START WITH 0;

CREATE DOMAIN egloo_module_feature_configs_ident_dom AS BIGINT
	DEFAULT NEXTVAL('egloo_module_feature_configs_ident_seq');

-- Allowed and Required Feature Configurations
CREATE TABLE egloo_module_feature_configs (
	egloo_module_feature_config_id	egloo_module_feature_configs_ident_dom,
	egloo_feature_config_id	BIGINT NOT NULL,
	egloo_module_id	BIGINT NOT NULL,
	required	BOOLEAN NOT NULL,
	allowed	BOOLEAN NOT NULL,
	created_timestamp	TIMESTAMPTZ DEFAULT current_timestamp NOT NULL,
	modified_timestamp	TIMESTAMPTZ DEFAULT current_timestamp NOT NULL,
	created_by	BIGINT DEFAULT 0 NOT NULL,
	modified_by	BIGINT DEFAULT 0 NOT NULL,
CONSTRAINT pk_egloo_module_feature_configs PRIMARY KEY (egloo_module_feature_configuration_id),
CONSTRAINT fk_egloo_module_feature_configs_egloo_feature_config_id FOREIGN KEY (egloo_feature_config_id)
	REFERENCES (egloo_feature_config_id)
	MATCH FULL
	ON DELETE NO ACTION
	ON UPDATE CASCADE,
CONSTRAINT fk_egloo_module_feature_configs_egloo_module_id FOREIGN KEY (egloo_module_id)
	REFERENCES egloo_modules(egloo_module_id)
	MATCH FULL
	ON DELETE NO ACTION
	ON UPDATE CASCADE,
CONSTRAINT u_egloo_module_feature_configs_module_to_feature_config UNIQUE (egloo_feature_config_id,egloo_module_id)
);


-- These are tables, views, sequences and so forth not sure about fields yet.
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