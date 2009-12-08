--Create the database versioning table
create table database_version ( 
	version_id serial not null,
	script_number int2 not null, 
	script_name varchar(32), 
	script_description varchar(128),
	CONSTRAINT version_id PRIMARY KEY (version_id)
)
WITHOUT OIDS;
ALTER TABLE database_version OWNER TO postgres;
GRANT SELECT, UPDATE, INSERT ON TABLE database_version TO webserver;
GRANT SELECT, UPDATE, INSERT, DELETE, REFERENCES, TRIGGER ON TABLE database_version TO postgres;

CREATE OR REPLACE VIEW searchProfiles AS (
  SELECT u.firstname as first, u.lastname as last, p.profile_id, p.profilename 
				FROM users u, profiles p where u.user_id = p.profilecreator

);
GRANT SELECT, INSERT, UPDATE, DELETE ON searchProfiles TO WebServer;

--Return profiles that meet search criteria 
CREATE OR REPLACE FUNCTION getSearchProfiles(IN input_name text, IN input_profile_count int8, IN input_start_index int8) RETURNS SETOF searchProfiles  AS
$BODY$
	DECLARE
		  	
           r_return searchProfiles;
	BEGIN
	
    FOR r_return IN SELECT c.first, c.last, c.profile_id, c.profilename 
    FROM searchProfiles c where c.first || ' ' || c.last ilike '%'||input_name||'%'  ORDER BY c.last LIMIT input_profile_count OFFSET input_start_index

	LOOP
		RETURN NEXT r_return;
	END LOOP;
END;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE;
ALTER FUNCTION getSearchProfiles(IN input_name text, IN input_profile_count int8, IN input_start_index int8) OWNER TO WebServer;

--Entering database version number and description.
insert into database_version(script_number, script_name, script_description) values(3, '4SearchPageUpdates.sql', 'VERSIONING TABLE and updates for the search page implemented');
