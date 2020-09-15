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
	include_once "php/utils/getUser.php";
	include_once "php/utils/getStarredProjects.php";
	include_once "php/utils/getAllLogs.php";

	$uid = null;
	$user = null;

	if(!empty($_SESSION['uid'])) {
		$uid = $_SESSION['uid'];
		$user = getUser($pdo, $uid);
	}else{
		header('Location: /napkin');
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

	<!-- Bootstrap CSS import -->
	<link rel="stylesheet" href="lib/bootstrap/bootstrap.min.css" />

	<!-- Custom styles -->
  <link rel="stylesheet" href="css/main.css" />
	<link rel="stylesheet" href="css/view.css" />

	<style type="text/css">
		/**/
	</style>

</head>
<body>

	<nav class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
		<a class="navbar-brand col-md-3 col-lg-2 mr-0 px-3" href="main">
			<img src="assets/logo.svg" width="30" height="30" class="d-inline-block align-top" alt="Napkin logo" />
		</a>

		<button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-toggle="collapse" data-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>

		<input class="form-control form-control-dark w-100" type="text" placeholder="Search" aria-label="Search" />

		<ul class="navbar-nav px-3">
			<li class="nav-item text-nowrap">
				<a class="nav-link" href="logout">Sign out</a>
			</li>
		</ul>
	</nav>

	<div class="container-fluid">
		<div class="row">
			<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
				<div class="sidebar-sticky pt-3">
					<ul class="nav flex-column">
						<li class="nav-item">
							<a class="nav-link active" href="main">
								<span data-feather="home"></span>
								Dashboard <span class="sr-only">(current)</span>
							</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="projects">
								<span data-feather="map"></span>
								Projects
							</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="data">
								<span data-feather="database"></span>
								Data sources
							</a>
						</li>
					</ul>

					<br />
					<!--h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
						<span>Saved reports</span>
						<a class="d-flex align-items-center text-muted" href="#" aria-label="Add a new report">
							<span data-feather="plus-circle"></span>
						</a>
					</h6-->

        	<ul class="nav flex-column mb-2">
          	<li class="nav-item">
            	<a class="nav-link" href="account">
              	<span data-feather="user"></span>
              	Account
            	</a>
          	</li>
          	<li class="nav-item">
            	<a class="nav-link" href="log">
              	<span data-feather="file-text"></span>
              	Logs
            	</a>
          	</li>
        	</ul>

					<br />

					<ul class="nav flex-column mb-2">
						<?php
							if($user['type'] == 'admin') {
								echo "
									<li class=\"nav-item\">
										<a class=\"nav-link\" href=\"admin\">
											<span data-feather=\"user-check\"></span>
											Administration
										</a>
									</li>

									<li class=\"nav-item\">
			            	<a class=\"nav-link\" href=\"settings\">
			              	<span data-feather=\"settings\"></span>
			              	Settings
			            	</a>
			          	</li>
								";
							}
						?>
          	<li class="nav-item">
            	<a class="nav-link" href="about">
              	<span data-feather="info"></span>
              	About
            	</a>
          	</li>
        	</ul>

					<br />

					<p class="text-muted text-center">
						<small>
							© <span id="ccYear">2020</span>
							<a href="https://napkingis.no" target="_blank">napkingis.no</a>.
							All rights reserved.
						</small>
					</p>
      	</div>
    	</nav>

    	<main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
      	<div class="row">
					<div class="col d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
	        	<h1 class="h2">Dashboard</h1>

	        	<div class="btn-toolbar mb-2 mb-md-0">
	          	<button type="button" class="btn btn-sm btn-outline-secondary" id="share">
								<span data-feather="share"></span>
							</button>
	        	</div>
	      	</div>
      	</div>

				<div class="row pt-3 pb-2 mb-3">
					<div class="col-md-6">
						<canvas class="w-100" id="sizeChart"></canvas>
					</div>

					<div class="col-md-6">
						<div class="card" style="max-height: 40vh;">
						  <div class="card-body" style="max-height: 30%; min-height: 90px;">
						    <h5 class="card-title">Starred</h5>
								<h6 class="card-subtitle text-muted">A list of your favorite projects</h6>
						  </div>
							<div class="list-group list-group-flush" style="max-height: 70%; overflow-y: auto;">
							  <?php
									$res = getStarredProjects($pdo, $uid);

									if(count($res) > 0) {
										foreach($res as $r) {
											echo "
												<a href=\"map?pid=".$r['projectid']."\" class=\"list-group-item list-group-item-action\">
													<div class=\"d-flex w-100 justify-content-between\">
											      <h5 class=\"mb-1\">".$r['name']."</h5>
											      <small>".date("d M. Y, H:i", strtotime($r['created_on']))."</small>
											    </div>
											    <small>".$r['description']."</small>
												</a>
											";
										}
									}else{
										echo "
											<a href=\"#\" class=\"list-group-item list-group-item-action disabled\" tabindex=\"-1\" aria-disabled=\"true\">
												<div class=\"d-flex w-100 justify-content-between\">
													<h5 class=\"mb-1\">No starred projects</h5>
												</div>
											</a>
										";
									}
								?>

							</div>
						</div>
					</div>
				</div>

				<div class="row pt-3 pb-2 mb-3">
					<div class="col">
						<p class="lead">Logs</p>

						<div class="table-responsive">
			        <table class="table table-sm mt-1">
								<caption>
									<a href="log" class="text-truncate">Show all records</a>
								</caption>
			          <thead>
			            <tr>
			              <th>#</th>
										<th>Type</th>
			              <th>Description</th>
			              <th>Entity</th>
										<th>Timestamp</th>
			            </tr>
			          </thead>
			          <tbody>
									<?php
										$res = getAllLogs($pdo, $uid);

										if(count($res) > 0) {
											$c = 0;

											foreach($res as $r) {
												if($c > 14) break;
												echo "
													<tr>
														<td><span class=\"text-truncate\">".$c++."</span></td>
														<td><span class=\"text-truncate\">".$r['type']."</span></td>
														<td><span class=\"text-truncate\">".$r['description']."</span></td>
														<td><span class=\"text-truncate\">".$r['entitytype']."</span></td>
														<td><span class=\"text-truncate\">".date("d M. Y, H:i", strtotime($r['timestamp']))."</span></td>
													</tr>
												";
											}
										}else{
											echo "
												<tr>
													<td></td>
													<td><span class=\"text-truncate text-muted\">No records found</span></td>
													<td></td> <td></td> <td></td>
												</tr>
											";
										}
									?>
			          </tbody>
			        </table>
			      </div>
					</div>
				</div>
    	</main>
  	</div>
	</div>

	<!-- jQuery - Popper.js - Bootstrap JS imports -->
  <script src="lib/jquery/jquery-3.5.1.min.js"></script>
  <script src="lib/popper/popper.min.js"></script>
  <script src="lib/bootstrap/bootstrap.min.js"></script>

	<script src="lib/feather/feather.min.js"></script>
	<script src="lib/chart/chart.min.js"></script>

	<script type="text/javascript">
    $("span#ccYear").html(new Date().getFullYear());
  </script>

	<script src="js/index.js"></script>

	<script src="js/main.php.js"></script>

</body>
</html>
