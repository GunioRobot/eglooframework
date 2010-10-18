-- Domains or types for Table IDs 
-- Seequences for domains
-- Constraint functions
-- Standard Meta Data
-- Sequences for IDs
-- MODIFIED BY to a nother table?
-- Hashes for passwords and security questions or row based encryption through the db?

-- Additional Constraints: created date not in the future, modified date not in the future

-- Conventions: make explicite constraints.
--				Null values should mean data is missing not that data has not been enetered (need to think about modified date)

CREATE TABLE users (
	user_id BIGINT,
	username VARCHAR(32) NOT NULL,
	user_password_hash VARCHAR(64),
	created_timestamp	TIMESTAMPTZ DEFAULT current_timestamp NOT NULL,
	modified_timestamp timestamptz DEFAULT NULL --Should be pushed to another table with modifications users_history or something.
CONSTRAINT users_username_nn NOT NULL (username)
CONSTRAINT pk_users PRIMARY KEY (user_id)
);

CREATE TABLE security_questions (
	security_question_id	BIGINT,
	security_question_hash	VARCHAR(64),
	security_question_answer_hash VARCHAR(64),
	created_timestamp	TIMESTAMPTZ DEFAULT current_timestamp NOT NULL,
	modified_timestamp timestamptz DEFAULT NULL --Should be pushed to another table with modifications users_history or something.
CONSTRAINT security_question_hash_nn NOT NULL (security_question_hash)
CONSTRAINT security_question_answer_hash_nn NOT NULL (security_question_answer_hash)
CONSTRAINT pk_security_questions (security_question_id)
);

CREATE TABLE user_security_questions (
	user_security_question_id BIGINT
	user_id BIGINT,
	security_question_id BIGINT
	created_timestamp	TIMESTAMPTZ DEFAULT current_timestamp NOT NULL,
	modified_timestamp timestamptz DEFAULT NULL --Should be pushed to another table with modifications users_history or something.
CONSTRAINT pk_user_security_question (user_security_question_id)
);

CREATE TABLE email_addresses (
	email_address_id BIGINT,
	email_address	VARCHAR(256), --Double check max length
	created_timestamp	TIMESTAMPTZ DEFAULT current_timestamp NOT NULL,
	modified_timestamp timestamptz DEFAULT NULL --Should be pushed to another table with modifications users_history or something.
CONSTRAINT pk_email_addresses (email_address_id)
);

-- Decide if users can have the same email address?
CREATE TABLE user_email_addresses (
	user_id BIGINT,
	email_address_id BIGINT
	created_timestamp	TIMESTAMPTZ DEFAULT current_timestamp NOT NULL,
	modified_timestamp timestamptz DEFAULT NULL --Should be pushed to another table with modifications users_history or something.	
CONSTRAINT pk_user_email_addresses (user_id,email_address_id)
);

CREATE TABLE mailing_addresses (

);

CREATE TABLE sessions (

);

CREATE TABLE emails (

);

CREATE TABLE products (

);

CREATE TABLE brands (

);

CREATE TABLE product_lines (

);

CREATE TABLE shopping_carts (

);

CREATE TABLE orders (

);

CREATE TABLE recurring_orders (

);

CREATE TABLE payments (

);

