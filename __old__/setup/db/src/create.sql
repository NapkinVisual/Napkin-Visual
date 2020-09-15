/*©agpl*************************************************************************
*                                                                              *
* Napkin Visual – Visualisation platform for the Napkin platform               *
* Copyright (C) 2020  Napkin AS                                                *
*                                                                              *
* This program is free software: you can redistribute it and/or modify         *
* it under the terms of the GNU Affero General Public License as published by  *
* the Free Software Foundation, either version 3 of the License, or            *
* (at your option) any later version.                                          *
*                                                                              *
* This program is distributed in the hope that it will be useful,              *
* but WITHOUT ANY WARRANTY; without even the implied warranty of               *
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the                 *
* GNU Affero General Public License for more details.                          *
*                                                                              *
* You should have received a copy of the GNU Affero General Public License     *
* along with this program.  If not, see <http://www.gnu.org/licenses/>.        *
*                                                                              *
*****************************************************************************©*/

/** commands to run before this script:
 *
 * $ CREATE EXTENSION IF NOT EXISTS "uuid-ossp";
 * $ CREATE EXTENSION IF NOT EXISTS "postgis";
 */

BEGIN;

CREATE TABLE IF NOT EXISTS "User"(
	userid uuid DEFAULT uuid_generate_v4(),
	type varchar(10) DEFAULT 'user',
	username varchar(45) NOT NULL,
	email varchar(45),
	passwd varchar(64) NOT NULL,
	created_on timestamp DEFAULT NOW(),
	last_login timestamp DEFAULT NOW(),

	PRIMARY KEY (userid)
);

CREATE TABLE IF NOT EXISTS "Project"(
	projectid uuid DEFAULT uuid_generate_v4(),
	name varchar(45) NOT NULL,
	description varchar(45),
	created_on timestamp DEFAULT NOW(),
	data json NOT NULL,
	aoi json NOT NULL,

	PRIMARY KEY (projectid)
);

END;



BEGIN;

CREATE TABLE IF NOT EXISTS "Log"(
	id uuid DEFAULT uuid_generate_v4(),
	entityid uuid NOT NULL,
	entitytype varchar(15) NOT NULL,
	type varchar(15) NOT NULL,
	description varchar(45) NOT NULL,
	timestamp timestamp DEFAULT NOW(),

	PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS "Datasource"(
	id uuid DEFAULT uuid_generate_v4(),
	ownerid uuid NOT NULL,
	type varchar(15) NOT NULL,
	name varchar(45) NOT NULL,
	description varchar(45),
	created_on timestamp DEFAULT NOW(),
	filepath text NOT NULL,

	PRIMARY KEY (id),
	FOREIGN KEY (ownerid)
		REFERENCES "User" (userid)
		ON DELETE CASCADE
		ON UPDATE CASCADE
);

END;



BEGIN;

CREATE TABLE IF NOT EXISTS "User_Project"(
	id uuid DEFAULT uuid_generate_v4(),
	userid uuid NOT NULL,
	projectid uuid NOT NULL,
	status varchar(15),
	starred boolean DEFAULT FALSE,

	PRIMARY KEY (id),
	FOREIGN KEY (userid)
		REFERENCES "User" (userid)
		ON DELETE CASCADE
		ON UPDATE CASCADE,
	FOREIGN KEY (projectid)
		REFERENCES "Project" (projectid)
		ON DELETE CASCADE
		ON UPDATE CASCADE
);

END;
