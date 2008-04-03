
CREATE OR REPLACE FUNCTION getRecentUpdateBlogProfiles(IN input_profile_count int8) RETURNS SETOF blogProfiles AS
$BODY$
	DECLARE
		  	
           r_return blogProfiles;
	BEGIN
	
 			FOR r_return IN SELECT blogwriter, blog_id, dateblogcreated, profilename FROM 
 			(SELECT DISTINCT on (c.blogwriter) c.blogwriter, c.blog_id,  c.dateblogcreated, c.profilename from blogProfiles c) 
 			as z order by blog_id DESC limit input_profile_count

	LOOP
		RETURN NEXT r_return;
	END LOOP;
END;
$BODY$
 LANGUAGE 'plpgsql' VOLATILE;
 ALTER FUNCTION getRecentUpdateBlogProfiles(IN input_profile_count int8) OWNER TO WebServer;
 
 --To allow upload of png and gif files.
 INSERT INTO MIMETypes VALUES ('image/png');
 INSERT INTO MIMETypes VALUES ('image/gif');