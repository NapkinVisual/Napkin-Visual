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

--

BEGIN;

INSERT INTO "User" (type, username, email, passwd)
VALUES
  (
    'admin',
    'andreas',
    'andreas@napkingis.no',
    '240f2ec6918381f9e7393b18539f8a2c8bc60b3224004a37ee7057a62abc2efa'
  );

INSERT INTO "Project" (name, description, data, aoi)
VALUES
  (
    'main',
    'main project',
    '{}',
    '{}'
  );

END;



BEGIN;

INSERT INTO "Log" (entityid, entitytype, type, description)
VALUES
  (
    (SELECT projectid from "Project" LIMIT 1),
    'Project',
    'create',
    'Create Project – Added a new project; main'
  );

INSERT INTO "Datasource" (ownerid, type, name, description, filepath)
VALUES
  (
    (SELECT userid from "User" LIMIT 1),
    'csv',
    'New datasource',
    'A new datasource',
    '/var/www/html/napkin/temp.csv'
  );

END;



BEGIN;

INSERT INTO "User_Project" (userid, projectid, status)
VALUES
  (
    (SELECT userid from "User" LIMIT 1),
    (SELECT projectid from "Project" LIMIT 1),
    'owner'
  );

END;
