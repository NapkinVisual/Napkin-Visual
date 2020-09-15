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
* along with this program. If not, see <http://www.gnu.org/licenses/>.         *
*                                                                              *
*****************************************************************************©*/

session_start();

//error_reporting(E_ALL); ini_set('display_errors',1); ini_set('error_reporting', E_ALL); ini_set('display_startup_errors',1); error_reporting(-1);
// uniqid();

include "utils/init_db.php";
include_once "utils/validateUID.php";
include_once "utils/security.php";
include_once "utils/createProject.php";
include_once "utils/getProject.php";
include_once "utils/getStarredProjects.php";
include_once "utils/getProjectSize.php";
include_once "utils/getAllProjects.php";
include_once "utils/saveProject.php";
include_once "utils/updateProject.php";
include_once "utils/deleteProject.php";


if(!isset($_SESSION['uid'])
|| !validateUID($pdo, $_SESSION['uid'])) {
  http_response_code(401);
  exit;
}

$uid = $_SESSION['uid'];


if(!isset($_REQUEST['op'])) {
  http_response_code(422);
	exit;
}

$op = $_REQUEST['op'];


if($op == "create")
{

  if(!isset($_POST['name'])
  || !isset($_POST['description'])
  || !isset($_POST['aoi'])) {
    http_response_code(422);
    exit;
  }

  $name = $_POST['name'];
  $description = $_POST['description'];
  $aoi = $_POST['aoi'];

  $res = createProject($pdo, $name, $description, $uid, $aoi);

  echo json_encode($res);

}
else
if($op == "get_sizes") {

  $rows = getAllProjects($pdo, $uid);

  $res = array();
  foreach($rows as $row) {
    array_push($res, array(
      "name" => $row['name'],
      "size" => getProjectSize($pdo, $row['projectid'])
    ));
  }

  echo json_encode($res);

}
else
if($op == "get"
|| $op == "get_all"
|| $op == "get_starred"
|| $op == "get_data"
|| $op == "star"
|| $op == "unstar"
|| $op == "save"
|| $op == "share"
|| $op == "update"
|| $op == "delete")
{

  if(!isset($_REQUEST['pid'])) {
    http_response_code(422);
    exit;
  }

  $pid = $_REQUEST['pid'];
  $accessControl = null;

  if($op == "get"
  || $op == "get_all"
  || $op == "get_starred"
  || $op == "get_data")
  {
    $accessControl = projectGetAccess($pdo, $uid, $pid);
  }
  else
  if($op == "star"
  || $op == "unstar"
  || $op == "save"
  || $op == "share"
  || $op == "update"
  || $op == "delete")
  {
    $accessControl = projectSetAccess($pdo, $uid, $pid);
  }


  if(!$accessControl) {
    http_response_code(401);
    exit;
  }



  if($op == "get")
  {

    $pid = $_GET['pid'];

    $res = getProject($pdo, $pid);

    echo json_encode($res);

  }
  else
  if($op == "get_all")
  {

    $pid = $_GET['pid'];

    $res = array( "info" => getProject($pdo, $pid) );

    $stmt = $pdo->prepare(
      "SELECT
        U.username,
        U.email
      FROM
        \"User_Project\" AS UP INNER JOIN
        \"User\" AS U
          ON UP.userid = U.userid
      WHERE
        UP.userid != ? AND
        UP.projectid = ?"
    );
    $stmt->execute([$uid, $pid]);
    $rows = $stmt->fetchAll();

    $res['shared'] = $rows;

    echo json_encode($res);

  }
  else
  if($op == "get_starred")
  {

    $res = getStarredProjects($pdo, $uid);

    echo json_encode($res);

  }
  else
  if($op == "get_data")
  {

    $pid = $_GET['pid'];

    $stmt = $pdo->prepare("SELECT data FROM \"Project\" WHERE projectid = ?");
    $stmt->execute([$pid]);
    $res = $stmt->fetch();

    echo json_encode($res['data']);

  }
  else
  if($op == "star")
  {

    $pid = $_POST['pid'];

    $stmt = $pdo->prepare("UPDATE \"User_Project\" SET starred = TRUE WHERE userid = ? AND projectid = ?");
    $res = $stmt->execute([$uid, $pid]);

    if($res) http_response_code(200);
    else http_response_code(500);

  }
  else
  if($op == "unstar")
  {

    $pid = $_POST['pid'];

    $stmt = $pdo->prepare("UPDATE \"User_Project\" SET starred = FALSE WHERE userid = ? AND projectid = ?");
    $res = $stmt->execute([$uid, $pid]);

    if($res) http_response_code(200);
    else http_response_code(500);

  }
  else
  if($op == "save")
  {

    if(!isset($_POST['data'])) {
      http_response_code(422);
      exit;
    }

    $pid = $_POST['pid'];
    $data = $_POST['data'];

    $res = saveProject($pdo, $pid, $data);

    if($res) http_response_code(200);
    else http_response_code(500);

  }
  else
  if($op == "share")
  {

    if(!isset($_POST['shareId'])) {
      http_response_code(422);
      exit;
    }

    $pid = $_POST['pid'];
    $shareId = $_POST['shareId'];

    $stmt = $pdo->prepare("INSERT INTO \"User_Project\" (userid, projectid, status) VALUES (?, ?, ?)");
    $res = $stmt->execute([$shareId, $pid, 'viewer']);

    if($res) http_response_code(200);
    else http_response_code(500);

  }
  else
  if($op == "update")
  {

    if(!isset($_POST['name'])
    || !isset($_POST['description'])
    || !isset($_POST['aoi'])) {
      http_response_code(422);
      exit;
    }

    $pid = $_POST['pid'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $aoi = $_POST['aoi'];

    $res = updateProject($pdo, $pid, $name, $description, $aoi);

    if($res) http_response_code(200);
    else http_response_code(500);

  }
  else
  if($op == "delete")
  {

    $pid = $_POST['pid'];

    $res = deleteProject($pdo, $pid);

    if($res) http_response_code(200);
    else http_response_code(500);

  }

}
else
{
  http_response_code(501);
}

exit;
