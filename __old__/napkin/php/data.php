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

include "utils/init_db.php";
include_once "utils/validateUID.php";
include_once "utils/security.php";
include_once "utils/createDatasource.php";
include_once "utils/getDatasource.php";
include_once "utils/getDatasourceOwner.php";
include_once "utils/updateDatasource.php";
include_once "utils/deleteDatasource.php";


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

  if(!isset($_POST['type'])
  || !isset($_POST['name'])
  || !isset($_POST['description'])
  || !isset($_FILES['file'])) {
    http_response_code(422);
    exit;
  }

  $type = $_POST['type'];
  $name = $_POST['name'];
  $description = $_POST['description'];
  $file = $_FILES['file'];

  if(!is_uploaded_file($file['tmp_name'])) throw new Exception("Error encountered while processing datafile", 1);

  $filename = uniqid();
  $path = dirname( dirname(__FILE__) )."/datafiles";
  $filepath = "$path/$filename";
  $res = move_uploaded_file($file['tmp_name'], $filepath);

  if(!$res) throw new Exception("Failed to upload datafile to server", 1);

  $res = createDatasource($pdo, $uid, $type, $name, $description, "datafiles/$filename");

  echo json_encode($res);

}
else
if($op == "get"
|| $op == "update"
|| $op == "delete")
{

  if(!isset($_REQUEST['did'])) {
    http_response_code(422);
    exit;
  }

  $did = $_REQUEST['did'];
  $accessControl = datasourceAccess($pdo, $uid, $did); // TODO: implement security checks in LDAP system


  if(!$accessControl) {
    http_response_code(401);
    exit;
  }



  if($op == "get")
  {

    $did = $_GET['did'];

    $res = getDatasource($pdo, $did);
    $res['owner'] = getDatasourceOwner($pdo, $did);

    echo json_encode($res);

  }
  else
  if($op == "update")
  {

    if(!isset($_POST['name'])
    || !isset($_POST['description'])) {
      http_response_code(422);
      exit;
    }

    $did = $_POST['did'];
    $type = null;
    $name = $_POST['name'];
    $description = $_POST['description'];

    if(isset($_FILES['file'])) {
      if(!isset($_POST['type'])) {
        http_response_code(422);
        exit;
      }

      $type = $_POST['type'];
      $file = $_FILES['file'];

      if(!is_uploaded_file($file['tmp_name'])) throw new Exception("Error encountered while processing datafile", 1);

      $res = getDatasource($pdo, $did);

      $filename = $res['filepath'];
      $path = dirname( dirname(__FILE__) )."/datafiles";
      $filepath = "$path/$filename";
      $res = move_uploaded_file($file['tmp_name'], $filepath);

      if(!$res) throw new Exception("Failed to upload datafile to server", 1);
    }

    $res = updateDatasource($pdo, $did, $type, $name, $description);

    if($res) http_response_code(200);
    else http_response_code(500);

  }
  else
  if($op == "delete")
  {

    $did = $_POST['did'];

    $res = getDatasource($pdo, $did);

    $path = dirname( dirname(__FILE__) );
    $filepath = "$path/".$res['filepath'];

    if(!file_exists($filepath)) throw new Exception("Error encountered while processing datafile", 1);
    $res = unlink($filepath);

    if(!$res) throw new Exception("Failed to delete datafile to server", 1);

    $res = deleteDatasource($pdo, $did);

    if($res) http_response_code(200);
    else http_response_code(500);

  }

}
else
{
  http_response_code(501);
}

exit;
