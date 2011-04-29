#! /usr/bin/env bash
#
# Example toolchain for converting a MySQL DB to a PostgreSQL DB
#
# NOT PRODUCTION READY SCRIPT - should serve as an example, nothing more

mysqldump -p -u someuser some_db > some_db_mysql_dump.sql

./mysql2pgsql.perl some_db_mysql_dump.sql some_db_pgsql_dump.sql

psql -q -U postgres some_db < some_db_pgsql_dump.sql > output_of_import.log