/**
 * Initial Schema File
 * 
 * Copyright 2010 eGloo, LLC
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *        http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *  
 * @author Matt brennan
 * @copyright 2010 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @version 0.1
 */

--********* DOES NOT COMPILE DO NOT USE YET WORK IN PROGRESS *************

-- Domains or types for Table IDs 
-- Seequences for domains
-- Constraint functions
-- Standard Meta Data
-- Sequences for IDs
-- MODIFIED BY to a nother table?
-- Hashes for passwords and security questions or row based encryption through the db?
-- Eventually include address validation via USPS, they provide tables I believe
-- Make address more robust, multiple countries with country based address verification

-- Additional Constraints: 	created date not in the future, modified date not in the future
--							Hashes need checks to ensure that they contain the proper types of characters
--							Not Null constraint for created timestamp?

-- Conventions: make explicite constraints.
--				Null values should mean data is missing not that data has not been enetered (need to think about modified date)
--				Table names should be plurals, make the names descriptive as possible so that laymen can understand them.

CREATE TABLE users (
	user_id BIGINT,
	username VARCHAR(32), -- Constraint needed
	user_password_hash VARCHAR(64), -- Constraint needed
	created_timestamp	TIMESTAMPTZ DEFAULT current_timestamp NOT NULL,
	modified_timestamp TIMESTAMPTZ DEFAULT NULL --Should be pushed to another table with modifications users_history or something.
CONSTRAINT users_username_nn NOT NULL (username),
CONSTRAINT users_user_password_hash_nn NOT NULL (user_password_hash),
CONSTRAINT pk_users PRIMARY KEY (user_id)
);

CREATE TABLE security_questions (
	security_question_id	BIGINT,
	security_question_hash	VARCHAR(64), -- Constraint needed
	security_question_answer_hash VARCHAR(64), -- Constraint needed
	created_timestamp	TIMESTAMPTZ DEFAULT current_timestamp NOT NULL,
	modified_timestamp TIMESTAMPTZ DEFAULT NULL --Should be pushed to another table with modifications users_history or something.
CONSTRAINT security_question_hash_nn NOT NULL (security_question_hash),
CONSTRAINT security_question_answer_hash_nn NOT NULL (security_question_answer_hash),
CONSTRAINT pk_security_questions (security_question_id)
);

CREATE TABLE user_security_questions (
	user_security_question_id BIGINT
	user_id BIGINT,
	security_question_id BIGINT
	created_timestamp	TIMESTAMPTZ DEFAULT current_timestamp NOT NULL,
	modified_timestamp TIMESTAMPTZ DEFAULT NULL --Should be pushed to another table with modifications users_history or something.
CONSTRAINT pk_user_security_question (user_security_question_id)
CONSTRAINT fk_user_security_questions_user_id FOREIGN KEY (user_id)
	REFERENCES users(user_id)
	MATCH FULL
	ON DELETE NO ACTION
	ON UPDATE CASCADE,
CONSTRAINT fk_user_security_questions_security_question_id FOREIGN KEY (security_question_id)
	REFERENCES security_questions(security_questions_id)
	MATCH FULL
	ON DELETE NO ACTION
	ON UPDATE CASCADE
);

CREATE TABLE email_addresses (
	email_address_id BIGINT,
	email_address	VARCHAR(256), --Double check max length, need constraint
	created_timestamp	TIMESTAMPTZ DEFAULT current_timestamp NOT NULL,
	modified_timestamp TIMESTAMPTZ DEFAULT NULL --Should be pushed to another table with modifications users_history or something.
CONSTRAINT pk_email_addresses (email_address_id)
);

-- Decide if users can have the same email address? for now no.
CREATE TABLE user_email_addresses (
	user_id BIGINT,
	email_address_id BIGINT
	created_timestamp	TIMESTAMPTZ DEFAULT current_timestamp NOT NULL,
	modified_timestamp TIMESTAMPTZ DEFAULT NULL --Should be pushed to another table with modifications users_history or something.	
CONSTRAINT pk_user_email_addresses (user_id,email_address_id),
CONSTRAINT fk_user_email_addresses_user_id FOREIGN KEY (user_id)
	REFERENCES users(user_id)
	MATCH FULL
	ON DELETE NO ACTION
	ON UPDATE CASCADE,
CONSTRAINT fk_email_address_id FOREIGN KEY (email_address_id)
	REFERENCES email_addresses(email_address_id)
	MATCH FULL
	ON DELETE NO ACTION
	ON UPDATE CASCADE
);

--Add provences or other normally included governmental entities used for shipping.
CREATE TABLE states (
	state_abbreviation	CHAR(2) --Needs check
	state_name	VARCHAR(80) -- Double check size.--Needs check
CONSTRAINT pk_states PRIMARY KEY (state_abbreviation),
CONSTRAINT states_state_abbreviation_nn NOT NULL (state_abbreviation),
CONSTRAINT states_state_name_nn	NOT NULL (state_name)
);

CREATE TABLE mailing_addresses (
	address_id	BIGINT,
	address_line1	VARCHAR (256), -- Double check size for this, needs check to prevent certian characters
	address_line2	VARCHAR (256), -- Needs check
	city	VARCHAR(64), -- Double check size for this, needs check
	state,	CHAR(2),
	zip_code	CHAR(9), -- Needs check, decide if this is the proper data format, 5 digit or 9 digit? or include the dash and 10 digit
	created_timestamp	TIMESTAMPTZ DEFAULT current_timestamp NOT NULL,
	modified_timestamp TIMESTAMPTZ DEFAULT NULL --Should be pushed to another table with modifications users_history or something.
CONSTRAINT pk_mailing_addresses PRIMARY KEY (address_id)
CONSTRAINT mailing_addresses_address_line1_nn NOT NULL (address_line1),
CONSTRAINT mailing_addresses_address_city_nn NOT NULL (city),
CONSTRAINT mailing_addresses_state_nn NOT NULL (state),
CONSTRAINT mailing_addresses_zip_code_nn NOT NULL (zip_code),
CONSTRAINT fk_mailing_addresses_state FOREIGN KEY (state)
	REFERENCES states(state_abbreviation)
	MATCH FULL
	ON DELETE NO ACTION
	ON UPDATE CASCADE
);

CREATE TABLE sessions (
	created_timestamp	TIMESTAMPTZ DEFAULT current_timestamp NOT NULL,
	modified_timestamp TIMESTAMPTZ DEFAULT NULL --Should be pushed to another table with modifications users_history or something.
);

CREATE TABLE identified_sessions (
	created_timestamp	TIMESTAMPTZ DEFAULT current_timestamp NOT NULL,
	modified_timestamp TIMESTAMPTZ DEFAULT NULL --Should be pushed to another table with modifications users_history or something.	
)

CREATE TABLE products (
	created_timestamp	TIMESTAMPTZ DEFAULT current_timestamp NOT NULL,
	modified_timestamp TIMESTAMPTZ DEFAULT NULL --Should be pushed to another table with modifications users_history or something.
);

--Product tags can be used to categorize products, set up hierachies 
CREATE TABLE product_tags (
	created_timestamp	TIMESTAMPTZ DEFAULT current_timestamp NOT NULL,
	modified_timestamp TIMESTAMPTZ DEFAULT NULL --Should be pushed to another table with modifications users_history or something.
);

CREATE TABLE product_sizes (
	created_timestamp	TIMESTAMPTZ DEFAULT current_timestamp NOT NULL,
	modified_timestamp TIMESTAMPTZ DEFAULT NULL --Should be pushed to another table with modifications users_history or something.	
);

CREATE TABLE brands (
	created_timestamp	TIMESTAMPTZ DEFAULT current_timestamp NOT NULL,
	modified_timestamp TIMESTAMPTZ DEFAULT NULL --Should be pushed to another table with modifications users_history or something.
);

CREATE TABLE product_lines (
	created_timestamp	TIMESTAMPTZ DEFAULT current_timestamp NOT NULL,
	modified_timestamp TIMESTAMPTZ DEFAULT NULL --Should be pushed to another table with modifications users_history or something.
);

CREATE TABLE shopping_carts (
	created_timestamp	TIMESTAMPTZ DEFAULT current_timestamp NOT NULL,
	modified_timestamp TIMESTAMPTZ DEFAULT NULL --Should be pushed to another table with modifications users_history or something.
);

CREATE TABLE products_in_shopping_cart (
	created_timestamp	TIMESTAMPTZ DEFAULT current_timestamp NOT NULL,
	modified_timestamp TIMESTAMPTZ DEFAULT NULL --Should be pushed to another table with modifications users_history or something.
);

CREATE TABLE orders (
	created_timestamp	TIMESTAMPTZ DEFAULT current_timestamp NOT NULL,
	modified_timestamp TIMESTAMPTZ DEFAULT NULL --Should be pushed to another table with modifications users_history or something.
);

CREATE TABLE recurring_orders (
	created_timestamp	TIMESTAMPTZ DEFAULT current_timestamp NOT NULL,
	modified_timestamp TIMESTAMPTZ DEFAULT NULL --Should be pushed to another table with modifications users_history or something.
);

CREATE TABLE payments (
	created_timestamp	TIMESTAMPTZ DEFAULT current_timestamp NOT NULL,
	modified_timestamp TIMESTAMPTZ DEFAULT NULL --Should be pushed to another table with modifications users_history or something.
);

CREATE TABLE warehouses (
	created_timestamp	TIMESTAMPTZ DEFAULT current_timestamp NOT NULL,
	modified_timestamp TIMESTAMPTZ DEFAULT NULL --Should be pushed to another table with modifications users_history or something.
);

CREATE TABLE shipments (
	created_timestamp	TIMESTAMPTZ DEFAULT current_timestamp NOT NULL,
	modified_timestamp TIMESTAMPTZ DEFAULT NULL --Should be pushed to another table with modifications users_history or something.	
);

CREATE TABLE coupons (
	created_timestamp	TIMESTAMPTZ DEFAULT current_timestamp NOT NULL,
	modified_timestamp TIMESTAMPTZ DEFAULT NULL --Should be pushed to another table with modifications users_history or something.	
);

CREATE TABLE product_coupons (
	created_timestamp	TIMESTAMPTZ DEFAULT current_timestamp NOT NULL,
	modified_timestamp TIMESTAMPTZ DEFAULT NULL --Should be pushed to another table with modifications users_history or something.
);

CREATE TABLE order_coupons (
	created_timestamp	TIMESTAMPTZ DEFAULT current_timestamp NOT NULL,
	modified_timestamp TIMESTAMPTZ DEFAULT NULL --Should be pushed to another table with modifications users_history or something.
);

