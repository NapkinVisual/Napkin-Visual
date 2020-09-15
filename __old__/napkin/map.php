<!--
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
-->

<?php
	//error_reporting(E_ALL); ini_set('display_errors',1); ini_set('error_reporting', E_ALL); ini_set('display_startup_errors',1); error_reporting(-1);

	session_start();

	include "php/utils/init_db.php";
	include_once "php/utils/security.php";

	$uid = null;
	$user = null;

	if(!empty($_SESSION['uid'])) {
		$uid = $_SESSION['uid'];
		$user = getUser($pdo, $uid);
	}else{
		header('Location: main');
	}

	$isViewer = false;
	if(!isset($_GET['pid'])) {
		header('Location: main');
	}else{
		$pid = $_GET['pid'];

		$accessControl = projectGetAccess($pdo, $uid, $pid);
		if(!$accessControl)
			header('Location: main');

		$accessControl = projectSetAccess($pdo, $uid, $pid);
		if(!$accessControl)
			$isViewer = true;
	}

	$isAdmin = false;
	if(isset($_GET['admin'])) {
		$isAdmin = $_GET['admin'] == $uid;
	}
?>


<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<meta http-equiv="x-ua-compatible" content="ie=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=no" />
  <meta name="description" content="User-friendly mapping" />
	<meta name="author" content="Napkin AS" />

	<title>Napkin – User-friendly mapping</title>

	<link rel="icon" href="assets/logo.svg" />

	<!-- Uber Font -->
  <link rel="stylesheet" href="lib/visual/superfine.css" />

  <!-- MapBox css -->
  <link href="lib/visual/mapbox-gl.css" rel="stylesheet" />

  <!-- Load React/Redux -->
  <script type="text/javascript" src="lib/visual/react.production.min.js"></script>
  <script type="text/javascript" src="lib/visual/react-dom.production.min.js"></script>
  <script type="text/javascript" src="lib/visual/redux.js"></script>
  <script type="text/javascript" src="lib/visual/react-redux.min.js"></script>
  <script type="text/javascript" src="lib/visual/styled-components.min.js"></script>

  <!-- Load build -->
  <script type="text/javascript" src="lib/visual/build.min.js"></script>

	<!-- Custom styles -->
  <link rel="stylesheet" href="css/map.css" />

	<style type="text/css">
		body {
			margin: 0;
			padding: 0;
			overflow: hidden;

			font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,"Noto Sans",sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol","Noto Color Emoji";
			font-size: 1rem;
			font-weight: 400;
			line-height: 1.5;
			text-align: left;
		}

		#alertArea {
			position: absolute;
			right: 15px;
			top: 15px;
			width: auto;
			z-index: 100;
		}

		#header {
			width: 100vw;
			height: 45px;
			background-color: rgb(41, 50, 60);
		}

		#header #left,
		#header #right {
			position: absolute;
			color: white;
		}

		#header #left {
			top: 10px;
			left: 15px;
		}

		#header #right {
			top: 12px;
			right: 15px;
		}

		#header #right a {
			color: inherit;
			text-decoration: none;
		}
		#header #right svg {
			margin-left: 10px;
		}

		#save {
			color: #6a7485;
		}

		#save:hover,
		#save:focus {
			cursor: pointer;
			color: lightgrey;
		}


		#__isViewer__ {
			display: none;
		}
	</style>

</head>
<body>

	<?php
		if($isViewer) echo "<div id=\"__isViewer__\"></div>";
	?>

	<div id="alertArea">
		<div class="alert alert-info alert-dismissible fade show" role="alert" id="loadingAlert">
		  <strong>Loading data.</strong> Please wait
		</div>
	</div>

	<header id="header">
    <div id="left">
      <a href="main">
        <img src="assets/logo.svg" width="25" height="25" alt="Napkin GIS logo" />
      </a>
    </div>

    <div id="right">
      <span id="save">
        <span data-feather="save" style="color: inherit;"></span>
      </span>
    </div>
  </header>

	<div id="app"></div>

	<!-- jQuery - Popper.js - Bootstrap JS imports -->
  <script src="lib/jquery/jquery-3.5.1.min.js"></script>
  <script src="lib/popper/popper.min.js"></script>
  <script src="lib/bootstrap/bootstrap.min.js"></script>

	<script src="lib/feather/feather.min.js"></script>

	<script src="js/map.php.js"></script>

</body>
</html>
