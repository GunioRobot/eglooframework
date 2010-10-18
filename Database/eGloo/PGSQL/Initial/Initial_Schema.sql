-- Domains or types for Table IDs 
-- Seequences for domains
-- Constraint functions
-- Standard Meta Data
-- Sequences for IDs
-- MODIFIED BY


CREATE TABLE users (
	user_id BIGINT
	created_timestamp	TIMESTAMPTZ DEFAULT current_timestamp NOT NULL,
	modified_timestamp timestamptz DEFAULT NULL
);

CREATE TABLE email_addresses (

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

