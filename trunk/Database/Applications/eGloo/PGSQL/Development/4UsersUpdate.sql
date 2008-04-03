--email address modification
create function lowercase_emailaddress() returns trigger as $lowercase_emailaddress$
begin 
        NEW.emailaddress := LOWER(NEW.emailaddress);
        RETURN NEW;
end;
$lowercase_emailaddress$ language plpgsql;
ALTER FUNCTION lowercase_emailaddress() OWNER TO postgres;

CREATE TRIGGER lowercase_emailaddress BEFORE INSERT OR UPDATE ON useremailaddresses
    FOR EACH ROW EXECUTE PROCEDURE lowercase_emailaddress();

