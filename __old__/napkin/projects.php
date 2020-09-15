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
	include_once "php/utils/searchProject.php";
	include_once "php/utils/getAllProjects.php";

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

	<!-- Leaflet CSS import -->
	<link rel="stylesheet" href="lib/leaflet/leaflet.css" />

	<!-- Custom styles -->
  <link rel="stylesheet" href="css/main.css" />
	<link rel="stylesheet" href="css/view.css" />

	<style type="text/css">
		.manageProject:hover {
			cursor: pointer;
		}

		#starProject[data-starred="1"],
		#starProject[data-starred="true"] {
			color: #ffb31a;
			fill: #ffb31a;
		}

		#map, #emap {
			width: 100%;
			height: 100%;
			min-height: 200px;
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

	<!-- new project modal -->
	<div class="modal fade" id="newProjectModal" tabindex="-1" role="dialog" aria-labelledby="newProjectModalLabel" aria-hidden="true">
	  <div class="modal-dialog modal-lg modal-dialog-scrollable">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title" id="newProjectModalLabel">Create new project</h5>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	      </div>
				<form id="createProjectForm" style="overflow-y: auto;">
		      <div class="modal-body">

						<div class="row">
							<div class="col-md-7">
								<div class="form-group">
							    <label for="projectName">Project name</label>
							    <input type="text" class="form-control" id="projectName" aria-describedby="projectName" required />
							  </div>
								<div class="form-group">
							    <label for="projectDescription">Project description</label>
							    <textarea class="form-control" id="projectDescription" rows="4"></textarea>
							  </div>
							</div>
							<div class="col-md-5">
								<label class="text-muted">Choose your area of interest</label>
								<div id="map"></div>
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
	        <h5 class="modal-title" id="infoModalLabel">Project info</h5>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	      </div>
	      <div class="modal-body container-fluid">

					<dl class="row">
					  <dd class="col-md-6">
							<dl class="row">
								<dt class="col-md-4 mb-3">Name</dt>
								<dd class="col-md-8"> <span id="projectName"></span> </dd>

								<dt class="col-md-4 mb-3">Description</dt>
								<dd class="col-md-8"> <span id="projectDescription"></span> </dd>

								<dt class="col-md-4 mb-3">Created on</dt>
								<dd class="col-md-8"> <span id="projectCreatedOn"></span> </dd>
							</dl>
						</dd>

					  <dd class="col-md-6">
					    <dl class="row">
					      <dt class="col-md-4 mb-3">Shared with:</dt>
					      <dd class="col-md-8"> <span id="projectShared"></span> </dd>
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

	<!-- share project modal -->
	<div class="modal fade" id="shareProjectModal" tabindex="-1" role="dialog" aria-labelledby="shareProjectModalLabel" aria-hidden="true">
	  <div class="modal-dialog modal-dialog-scrollable">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title" id="shareProjectModalLabel">Share project</h5>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	      </div>
				<form id="shareProjectForm" data-projectid="" style="overflow-y: auto;">
		      <div class="modal-body">

						<div class="row">
							<div class="col">
								<div class="form-group">
							    <label for="shareName">Username</label>
							    <input type="text" class="form-control" id="shareName" aria-describedby="shareName" required />
							  </div>
							</div>
						</div>

						<div class="row">
							<div class="col">
								<div class="list-group" id="userList">
									<button type="button" class="list-group-item list-group-item-action" disabled>No entities found</button>
								</div>
							</div>
						</div>

						<br />

		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
		      </div>
				</form>
	    </div>
	  </div>
	</div>

	<!-- edit project modal -->
	<div class="modal fade" id="editProjectModal" tabindex="-1" role="dialog" aria-labelledby="editProjectModalLabel" aria-hidden="true">
	  <div class="modal-dialog modal-lg modal-dialog-scrollable">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title" id="editProjectModalLabel">Edit project</h5>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	      </div>
				<form id="editProjectForm" style="overflow-y: auto;">
		      <div class="modal-body">

						<div class="row">
							<div class="col-md-7">
								<div class="form-group">
							    <label for="projectName">Project name</label>
							    <input type="text" class="form-control" id="projectName" aria-describedby="projectName" required />
							  </div>
								<div class="form-group">
							    <label for="projectDescription">Project description</label>
							    <textarea class="form-control" id="projectDescription" rows="4"></textarea>
							  </div>
							</div>
							<div class="col-md-5">
								<label class="text-muted">Choose your area of interest</label>
								<div id="emap"></div>
							</div>
						</div>
						<br />

		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
		        <button type="submit" class="btn btn-primary" id="editProject" data-projectid="">Save</button>
		      </div>
				</form>
	    </div>
	  </div>
	</div>

	<!-- delete project modal -->
	<div class="modal fade" id="deleteProjectModal" tabindex="-1" role="dialog" aria-labelledby="deleteProjectModalLabel" aria-hidden="true">
	  <div class="modal-dialog modal-dialog-scrollable">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title" id="deleteProjectModalLabel">Delete project</h5>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	      </div>
	      <div class="modal-body">
					<p class="lead text-justify">Are you sure you want to delete?</p>
	      </div>
				<div class="modal-footer">
	        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
	        <button type="button" class="btn btn-danger" id="deleteProject" data-dismiss="modal" data-projectid="">Delete</button>
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
							<a class="nav-link <?php if(!$isAdmin) echo "active"; ?>" href="projects">
								<span data-feather="map"></span>
								Projects <?php if(!$isAdmin) echo "<span class=\"sr-only\">(current)</span>"; ?>
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
							Projects
						</h1>

	        	<div class="btn-toolbar mb-2 mb-md-0" role="toolbar">
	          	<?php
								if(!$isAdmin) {
									echo "
										<button type=\"button\" class=\"btn btn-sm btn-outline-primary\" data-toggle=\"modal\" data-target=\"#newProjectModal\">
											<strong>New project</strong>
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

							<button type="submit" class="btn btn-sm btn-outline-secondary mb-2">Search</button>
						</form>

						<br />

						<div class="table-responsive">
							<table class="table table-striped">
								<thead>
									<tr>
										<th>#</th>
										<th>Name</th>
										<th>Description</th>
										<th>Created on</th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
									</tr>
								</thead>
								<tbody id="projectTable">
									<?php
										$res = null;
										$op = "";
										if(isset($_GET['op'])) $op = $_GET['op'];

										if($op == "search") {
											$name = $_GET['name'];
										  $dateFrom = $_GET['createdFrom'];
										  $dateTo = $_GET['createdTo'];

										  if(empty($_GET['name'])) $name = "_";
										  if(empty($_GET['createdFrom'])) $dateFrom = null;
										  if(empty($_GET['createdTo'])) $dateTo = null;

										  $res = searchProject($pdo, $uid, $name, $dateFrom, $dateTo);
									  }else{
											if($isAdmin) {
												$stmt = $pdo->prepare(
											    "SELECT
											      *
											    FROM
											      \"Project\"
											    ORDER BY
											      created_on DESC"
											  );
											  $stmt->execute();
											  $res = $stmt->fetchAll();
											}

											else $res = getAllProjects($pdo, $uid);
										}

										if(count($res) > 0) {
											foreach($res as $r) {
												echo "
													<tr id=\"projectRow\" data-projectid=\"".$r['projectid']."\">
														<th scope=\"row\">
												";

												if(!$isAdmin) {
													echo "
														<span
															data-feather=\"star\"
															class=\"manageProject\"
															id=\"starProject\"
															data-projectid=\"".$r['projectid']."\"
															data-starred=\"".$r['starred']."\"
														></span>
													";
												}

												echo "
														</th>
														<td>
															<a
																class=\"text-truncate\"
																href=\"map?pid=".$r['projectid']."\"
															>
																".$r['name']."
															</a>
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
																class=\"manageProject\"
																id=\"infoProject\"
																data-projectid=\"".$r['projectid']."\"
															></span>
														</td>
														<td>
															<span
																data-feather=\"share-2\"
																class=\"manageProject\"
																id=\"shareProject\"
																data-projectid=\"".$r['projectid']."\"
																data-toggle=\"modal\"
																data-target=\"#shareProjectModal\"
															></span>
														</td>
														<td>
															<span
																data-feather=\"edit-2\"
																class=\"manageProject\"
																id=\"editProject\"
																data-projectid=\"".$r['projectid']."\"
																data-toggle=\"modal\"
																data-target=\"#editProjectModal\"
															></span>
														</td>
														<td>
															<span
																data-feather=\"trash\"
																class=\"manageProject\"
																id=\"deleteProject\"
																data-projectid=\"".$r['projectid']."\"
																data-toggle=\"modal\"
																data-target=\"#deleteProjectModal\"
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
															No projects found
														</span>
													</td>
													<td></td> <td></td> <td></td> <td></td> <td></td>
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

	<script src="lib/leaflet/leaflet.js"></script>

	<script type="text/javascript">
    $("span#ccYear").html(new Date().getFullYear());
  </script>

	<script src="js/index.js"></script>

	<script src="js/projects.php.js"></script>

</body>
</html>
