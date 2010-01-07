-- CREATE LANGUAGE 'plpgsql';

CREATE OR REPLACE FUNCTION make_plpgsql()
RETURNS VOID
LANGUAGE SQL
AS $$
CREATE LANGUAGE plpgsql;
$$;
 
SELECT
    CASE
    WHEN EXISTS(
        SELECT 1
        FROM pg_catalog.pg_language
        WHERE lanname='plpgsql'
    )
    THEN NULL
    ELSE make_plpgsql() END;
 
DROP FUNCTION make_plpgsql();

CREATE TABLE Sessions (
	Session_ID															VARCHAR(32) NOT NULL, -- Alpha numeric 32 chars long.
	DateAccessed														TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
	UserAgent															VARCHAR(256) NOT NULL, -- Will there be a table of user agents? NO! *TEAR*
	SessionData															TEXT NOT NULL,  -- Need more info about how sessions work before changing this.
CONSTRAINT pk_Sessions_Session_ID PRIMARY KEY (Session_ID)
);

CREATE TABLE IdentifiedSessions (
	Session_ID															VARCHAR(32) NOT NULL,
	User_ID																BIGINT NOT NULL,
CONSTRAINT pk_IdentifiedSessions PRIMARY KEY (Session_ID),
CONSTRAINT fk_IdentifiedSessions_Session_ID FOREIGN KEY (Session_ID)
	REFERENCES Sessions(Session_ID)
	MATCH FULL
	ON DELETE CASCADE -- this cascades so that don't need to modify deleteOldSessoins function
	ON UPDATE CASCADE,
CONSTRAINT fk_IdentifiedSessions_User_ID FOREIGN KEY (User_ID)
	REFERENCES Users(User_ID)
	MATCH FULL
	ON DELETE NO ACTION
	ON UPDATE CASCADE
);	

 -- $userLogin$ LANGUAGE 'plpgsql';

-- User enters the site.  First time they come in php is like yo gimme cookie. 
-- they don't have a session, php generates a session and makes an ID up.  Sets a session cookie and puts the session ID in the cookie, then sends it 
-- to the user.  Everytime the user enters the site it will use the session id to

-- If successful return boolean of success.

-- 32 digits.
-- 4561ff7961dc7b2d9a845c6bfb7d1323
-- ad19a56c4d09191d6e58b01b87225468
-- d152c2fb7115ebf1b353235ad1292fa7
-- True for succeded + false failure.
CREATE OR REPLACE FUNCTION setSession (input_Session_ID TEXT, input_User_ID BIGINT, input_UserAgent TEXT, input_SessionData TEXT) RETURNS BOOLEAN AS $setSession$

	DECLARE
		
	BEGIN
		UPDATE Sessions
		SET SessionData = input_SessionData, DateAccessed = CURRENT_TIMESTAMP
		WHERE Session_ID = input_Session_ID;
		
		IF NOT FOUND THEN
			INSERT INTO Sessions (Session_ID, UserAgent, SessionData)
			VALUES (input_Session_ID, input_UserAgent, input_SessionData);
			
			IF input_User_ID NOTNULL THEN
				INSERT INTO IdentifiedSessions (Session_ID, User_ID)
				VALUES (input_Session_ID, input_User_ID);
			END IF;
		END IF;
		
	RETURN FOUND;
	
	END;

-- $setSession$ LANGUAGE 'plpgsql';

-- Gets data on a session and updates its access date.
CREATE OR REPLACE FUNCTION getSession (input_Session_ID TEXT, OUT output_Session_ID TEXT, OUT output_User_ID BIGINT, OUT output_UserAgent TEXT, OUT output_SessionData TEXT, OUT output_SessionExists BOOLEAN) AS $getSession$

	DECLARE
			
	BEGIN
		UPDATE Sessions
		SET DateAccessed = CURRENT_TIMESTAMP
		WHERE Session_ID = input_Session_ID;

		SELECT INTO 
		output_Session_ID,		output_User_ID,		output_UserAgent, 	output_SessionData 
		Sessions.Session_ID,	User_ID,			UserAgent, 			SessionData
		FROM Sessions LEFT JOIN IdentifiedSessions ON Sessions.Session_ID = IdentifiedSessions.Session_ID
		WHERE Sessions.Session_ID = input_Session_ID;
		
		output_SessionExists:=FOUND;
				
--		SELECT INTO 
--		output_UserAgent, 	output_SessionData 
--		UserAgent, 			SessionData
--		FROM Sessions
--		WHERE Session_ID = inoutput_Session_ID;
		
--		SELECT INTO 
--		output_User_ID
--		User_ID
--		FROM IdentifiedSessions
--		WHERE Session_ID = inoutput_Session_ID;
	
	END;

-- $getSession$ LANGUAGE 'plpgsql';

--Deletes a specific session from the database
CREATE OR REPLACE FUNCTION deleteSession (input_Session_ID TEXT) RETURNS BOOLEAN AS $deleteSession$

	DECLARE
	
	BEGIN
		DELETE FROM Sessions 
		WHERE Session_ID = input_Session_ID;
	
	RETURN TRUE;
	
	END;

-- $deleteSession$ LANGUAGE 'plpgsql';

-- Deletes outdated sessions from the database.
-- Should return the number of sessions that are deleted.
-- Should also let the sequence know what session ids are freed up.
CREATE OR REPLACE FUNCTION deleteOldSessions (input_SessionLifeTime INTERVAL) RETURNS BOOLEAN AS $deleteOldSessions$

-- I am going to be passed integer of minutes.
-- I must cast into interval.

	DECLARE
	
	BEGIN
		DELETE FROM Sessions WHERE (CURRENT_TIMESTAMP-DateAccessed)>=input_sessionLifeTime;	
	
	RETURN TRUE;
	
	END;
	
-- $deleteOldSessions$ LANGUAGE 'plpgsql';-- This first role is what the webservers will use to log in and access/change information in the database.
