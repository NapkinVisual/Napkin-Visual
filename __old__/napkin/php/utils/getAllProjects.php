<?php
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

//include "init_db.php";

//include_once "getProject.php";


function getAllProjects($pdo, $uid) {
  $stmt = $pdo->prepare(
    "SELECT
      P.projectid,
      P.name,
      P.description,
      P.created_on,
      P.data,
      P.aoi,
      UP.starred
    FROM
      \"User_Project\" AS UP INNER JOIN
      \"Project\" AS P
        ON UP.projectid = P.projectid
    WHERE
      userid = ?
    ORDER BY
      P.created_on DESC"
  );
  $stmt->execute([$uid]);
  $rows = $stmt->fetchAll();

  return $rows;
}
