-- Domains or types for Table IDs 
-- Seequences for domains
-- Constraint functions
-- Standard Meta Data
-- Sequences for IDs
-- MODIFIED BY

-- Conventions: make explicite constraints.
--				Null values should mean data is missing not that data has not been enetered (need to think about modified date)

CREATE TABLE users (
	user_id BIGINT
	username VARCHAR(32) NOT NULL,
	user_password_hash VARCHAR(64),
	created_timestamp	TIMESTAMPTZ DEFAULT current_timestamp NOT NULL,
	modified_timestamp timestamptz DEFAULT NULL --Should be pushed to another table with modifications users_history or something.
CONSTRAINT users_username_nn NOT NULL (username)
CONSTRAINT pk_users PRIMARY KEY (user_id)
CONSTRAINT 
-- Additional Constraints: created date not in the future, modified date not in the future
);

CREATE TABLE security_questions

CREATE TABLE email_addresses (
	-- May wish to create an email ID for increased join speed.
	email_address	VARCHAR(256) --Double check max length
	created_timestamp	TIMESTAMPTZ DEFAULT current_timestamp NOT NULL,
	modified_timestamp timestamptz DEFAULT NULL --Should be pushed to another table with modifications users_history or something.
CONSTRAINT pk_email_addresses (email_address)
);

CREATE TABLE user_email_addresses (
	user_id
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

