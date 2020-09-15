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
	include_once "php/utils/searchDatasource.php";
	include_once "php/utils/getAllDatasources.php";

	$uid = null;
	$user = null;

	if(!empty($_SESSION['uid'])) {
		$uid = $_SESSION['uid'];
		$user = getUser($pdo, $uid);
	}else{
		header('Location: /napkin');
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

	<!-- Bootstrap CSS import -->
	<link rel="stylesheet" href="lib/bootstrap/bootstrap.min.css" />

	<!-- Bootstrap-datepicker CSS import -->
	<link rel="stylesheet" href="lib/bootstrap-datepicker/bootstrap-datepicker.min.css" />

	<!-- Custom styles -->
  <link rel="stylesheet" href="css/main.css" />
	<link rel="stylesheet" href="css/view.css" />

	<style type="text/css">
		.manageDatasource:hover {
			cursor: pointer;
		}

		#upload {
			width: 100%;
			height: 100%;
			min-height: 100px;
		}
	</style>

</head>
<body>

	<!-- loading modal -->
	<div class="modal fade" id="loadingModal" tabindex="-1" role="dialog" aria-labelledby="loadingModalLabel" aria-hidden="true">
	  <div class="modal-dialog modal-dialog-scrollable">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title" id="loadingModalLabel">Loading...</h5>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	      </div>
	      <div class="modal-body">

					<div class="spinner-border text-primary" role="status">
						<span class="sr-only">Loading...</span>
					</div>

	      </div>
	    </div>
	  </div>
	</div>

	<!-- new datasource modal -->
	<div class="modal fade" id="newDatasourceModal" tabindex="-1" role="dialog" aria-labelledby="newDatasourceModalLabel" aria-hidden="true">
	  <div class="modal-dialog modal-lg modal-dialog-scrollable">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title" id="newDatasourceModalLabel">Create new datasource</h5>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	      </div>
				<form enctype="multipart/form-data" id="createDatasourceForm">
		      <div class="modal-body" style="overflow-y: auto;">

						<div class="row">
							<div class="col-md-7">
								<div class="form-group">
							    <label for="datasourceName">Name</label>
							    <input type="text" class="form-control" id="datasourceName" aria-describedby="datasourceName" required />
							  </div>
								<div class="form-group">
							    <label for="datasourceDescription">Description</label>
							    <textarea class="form-control" id="datasourceDescription" rows="4"></textarea>
							  </div>
							</div>
							<div class="col-md-5">
								<div class="dropzone" id="upload">
									<p class="text-muted mt-5"><small id="dropzoneCont">Drop your datafile (.csv, .xlsx, .geojson, .shp, .sap)</small></p>
								</div>
								<input type="file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel, application/json, application/geo+json, application/x-esri-shape" id="upload" style="display: none;" required />
							</div>
						</div>
						<br />

		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
		        <button type="submit" class="btn btn-primary">Create</button>
		      </div>
				</form>
	    </div>
	  </div>
	</div>

	<!-- info modal -->
	<div class="modal fade" id="infoModal" tabindex="-1" role="dialog" aria-labelledby="infoModalLabel" aria-hidden="true">
	  <div class="modal-dialog modal-lg modal-dialog-scrollable">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title" id="infoModalLabel">Datasource info</h5>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	      </div>
	      <div class="modal-body container-fluid">

					<dl class="row">
					  <dd class="col-md-6">
							<dl class="row">
								<dt class="col-md-4 mb-3">Type</dt>
								<dd class="col-md-8"> <span id="datasourceType"></span> </dd>

								<dt class="col-md-4 mb-3">Name</dt>
								<dd class="col-md-8"> <span id="datasourceName"></span> </dd>

								<dt class="col-md-4 mb-3">Description</dt>
								<dd class="col-md-8"> <span id="datasourceDescription"></span> </dd>

								<dt class="col-md-4 mb-3">Created on</dt>
								<dd class="col-md-8"> <span id="datasourceCreatedOn"></span> </dd>
							</dl>
						</dd>

					  <dd class="col-md-6">
					    <dl class="row">
								<?php
									if($isAdmin) {
										echo "
											<dt class=\"col-md-4 mb-3\">Owner:</dt>
											<dd class=\"col-md-8\">
												<span id=\"datasourceOwner\"></span>
											</dd>
										";
									}
								?>

					      <dt class="col-md-4 mb-3">Datafile:</dt>
					      <dd class="col-md-8"> <span id="datasourceFile"></span> </dd>
					    </dl>
					  </dd>
					</dl>

	      </div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				</div>
	    </div>
	  </div>
	</div>

	<!-- edit datasource modal -->
	<div class="modal fade" id="editDatasourceModal" tabindex="-1" role="dialog" aria-labelledby="editDatasourceModalLabel" aria-hidden="true">
	  <div class="modal-dialog modal-lg modal-dialog-scrollable">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title" id="editDatasourceModalLabel">Edit datasource</h5>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	      </div>
				<form id="editDatasourceForm" style="overflow-y: auto;">
		      <div class="modal-body">

						<div class="row">
							<div class="col-md-7">
								<div class="form-group">
							    <label for="datasourceName">Name</label>
							    <input type="text" class="form-control" id="datasourceName" aria-describedby="datasourceName" required />
							  </div>
								<div class="form-group">
							    <label for="datasourceDescription">Description</label>
							    <textarea class="form-control" id="datasourceDescription" rows="4"></textarea>
							  </div>
							</div>
							<div class="col-md-5">
								<div class="dropzone" id="upload">
									<p class="text-muted mt-5"><small id="dropzoneCont">Drop your datafile (.csv, .xlsx, .geojson, .shp, .sap)</small></p>
								</div>
								<input type="file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel, application/json, application/geo+json, application/x-esri-shape" id="upload" style="display: none;" />
							</div>
						</div>
						<br />

		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
		        <button type="submit" class="btn btn-primary" id="editDatasource" data-datasourceid="">Save</button>
		      </div>
				</form>
	    </div>
	  </div>
	</div>

	<!-- delete datasource modal -->
	<div class="modal fade" id="deleteDatasourceModal" tabindex="-1" role="dialog" aria-labelledby="deleteDatasourceModalLabel" aria-hidden="true">
	  <div class="modal-dialog modal-dialog-scrollable">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title" id="deleteDatasourceModalLabel">Delete datasource</h5>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	      </div>
	      <div class="modal-body">
					<p class="lead text-justify">Are you sure you want to delete?</p>
	      </div>
				<div class="modal-footer">
	        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
	        <button type="button" class="btn btn-danger" id="deleteDatasource" data-dismiss="modal" data-datasourceid="">Delete</button>
	      </div>
	    </div>
	  </div>
	</div>




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
							<a class="nav-link" href="main">
								<span data-feather="home"></span>
								Dashboard
							</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="projects">
								<span data-feather="map"></span>
								Projects
							</a>
						</li>
						<li class="nav-item">
							<a class="nav-link <?php if(!$isAdmin) echo "active"; ?>" href="data">
								<span data-feather="database"></span>
								Data sources <?php if(!$isAdmin) echo "<span class=\"sr-only\">(current)</span>"; ?>
							</a>
						</li>
					</ul>

					<br />

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

    	<main role="main" class="col-md-9 col-lg-10 ml-sm-auto px-md-4">
      	<div class="row">
					<div class="col d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
	        	<h1 class="h2">
							<?php if($isAdmin) echo "<span data-feather=\"user-check\" style=\"width: 1em; height: 1em; color: #007bff;\"></span>"; ?>
							Datasources
						</h1>

	        	<div class="btn-toolbar mb-2 mb-md-0">
	          	<?php
								if(!$isAdmin) {
									echo "
										<button type=\"button\" class=\"btn btn-sm btn-outline-primary\" data-toggle=\"modal\" data-target=\"#newDatasourceModal\">
											<strong>New datasource</strong>
											<span data-feather=\"plus\"></span>
										</button>

										&nbsp;
									";
								}
							?>

							<button type="button" class="btn btn-sm btn-outline-secondary" id="share">
								<span data-feather="share"></span>
							</button>
	        	</div>
	      	</div>
      	</div>

				<div class="row pt-3 pb-2 mb-3">
					<div class="col">
						<form class="form-inline" method="GET" action="">
							<input type="hidden" name="op" value="search" />
							<?php
								if($isAdmin)
									echo "<input type=\"hidden\" name=\"admin\" value=\"".$uid."\" />";
							?>

							<input type="text" class="form-control form-control-sm mb-2 mr-sm-2" name="name" placeholder="Name" />

							<input type="text" class="form-control form-control-sm mb-2 mr-sm-2" name="createdFrom" id="createdFrom" placeholder="From" />
							<input type="text" class="form-control form-control-sm mb-2 mr-sm-2" name="createdTo" id="createdTo" placeholder="To" />

							<select class="form-control form-control-sm custom-select custom-select-sm mb-2 mr-sm-2" name="type">
								<option value="" selected disabled hidden>Choose type</option>
								<option value="csv">CSV file</option>
								<option value="xlsx">Excel</option>
								<option value="geojson">GeoJSON file</option>
								<option value="shp">Shape file</option>
							</select>

							<button type="submit" class="btn btn-sm btn-outline-secondary mb-2">Search</button>
						</form>

						<br />

						<div class="table-responsive">
							<table class="table table-striped table-sm">
								<thead>
									<tr>
										<th>#</th>
										<th>Type</th>
										<th>Name</th>
										<th>Description</th>
										<th>Created on</th>
										<th></th>
										<th></th>
										<th></th>
									</tr>
								</thead>
								<tbody>
									<?php
										$res = null;
										$op = "";
										if(isset($_GET['op'])) $op = $_GET['op'];

										if($op == "search") {
											$name = $_GET['name'];
											$dateFrom = $_GET['createdFrom'];
											$dateTo = $_GET['createdTo'];
											if(isset($_GET['type'])) $type = $_GET['type'];

											if(empty($_GET['name'])) $name = "_";
											if(empty($_GET['createdFrom'])) $dateFrom = null;
											if(empty($_GET['createdTo'])) $dateTo = null;
											if(empty($type)) $type = null;

											$res = searchDatasource($pdo, $uid, $name, $dateFrom, $dateTo, $type);
										}else{
											$res = getAllDatasources($pdo, $uid);
										}

										if(count($res) > 0) {
											foreach($res as $r) {
												echo "
													<tr id=\"datasourceRow\" data-datasourceid=\"".$r['id']."\">
														<th scope=\"row\"></th>
														<td>
															<span class=\"text-truncate\">
																".$r['type']."
															</span>
														</td>
														<td>
															<span class=\"text-truncate\">
																".$r['name']."
															</span>
														</td>
														<td>
															<span class=\"text-truncate\">
																".$r['description']."
															</span>
														</td>
														<td>
															<span class=\"text-truncate\">
																".date("d M. Y, H:i", strtotime($r['created_on']))."
															</span>
														</td>
														<td>
															<span
																data-feather=\"info\"
																class=\"manageDatasource\"
																id=\"infoDatasource\"
																data-datasourceid=\"".$r['id']."\"
															></span>
														</td>
														<td>
															<span
																data-feather=\"edit-2\"
																class=\"manageDatasource\"
																id=\"editDatasource\"
																data-datasourceid=\"".$r['id']."\"
																data-toggle=\"modal\"
																data-target=\"#editDatasourceModal\"
															></span>
														</td>
														<td>
															<span
																data-feather=\"trash\"
																class=\"manageDatasource\"
																id=\"deleteDatasource\"
																data-datasourceid=\"".$r['id']."\"
																data-toggle=\"modal\"
																data-target=\"#deleteDatasourceModal\"
															></span>
														</td>
													</tr>
												";
											}
										}else{
											echo "
												<tr>
													<th scope=\"row\"></th>
													<td>
														<span class=\"text-truncate text-muted\">
															No datasources found
														</span>
													</td>
													<td></td> <td></td> <td></td> <td></td> <td></td> <td></td>
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
	<script src="lib/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>

	<script type="text/javascript">
    $("span#ccYear").html(new Date().getFullYear());
  </script>

	<script src="js/index.js"></script>

	<script src="js/data.php.js"></script>

</body>
</html>
