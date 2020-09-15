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

"use strict";

window.addEventListener("load", function() {
  feather.replace();
  init();


  $("#createdFrom").datepicker({ orientation: "bottom" });
  $("#createdTo").datepicker({ orientation: "bottom" });

  let lmap = L.map("map", {
    center: [60.8235, 8.2206],
    zoom: 5,
    zoomControl: false,
    layers: [
      L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        attribution: "&copy; <a href=\"https://www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors"
      })
    ]
  });

  let emap = L.map("emap", {
    center: [60.8235, 8.2206],
    zoom: 5,
    zoomControl: false,
    layers: [
      L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        attribution: "&copy; <a href=\"https://www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors"
      })
    ]
  });

  $("#newProjectModal").on("shown.bs.modal", (ev) => lmap.invalidateSize());
  $("#editProjectModal").on("shown.bs.modal", (ev) => emap.invalidateSize());


  $("#starProject").click(function(ev) {
    let projectid = $(this).attr("data-projectid"),
        starred = parseInt($(this).attr("data-starred")) == 1;
    let op = "", starredVal;

    if(starred) {
      op = "unstar";
      starredVal = "";
    }else{
      op = "star";
      starredVal = "1";
    }

    let self = this;
    $.ajax({
      type: "POST",
      url: "php/project",
      data: {
        "op": op,
        "pid": projectid
      },
      success: function(result, status, xhr) {
        console.log(status);
        $(self).attr("data-starred", starredVal);
      },
      error: function(xhr, status, error) {
        console.log(xhr.status);
        console.log(error);
      }
    });
  });


  $("#newProjectModal form#createProjectForm").submit(function(ev) {
    ev.preventDefault();

    $("#newProjectModal").modal("hide");

    let name = $("#newProjectModal input#projectName").val(),
        description = $("#newProjectModal textarea#projectDescription").val(),
        center = emap.getCenter(),
        zoom = emap.getZoom();
    let aoi = {
          latitude: center.lat,
          longitude: center.lng,
          zoom: zoom
        };

    $("#loadingModal").modal("show");

    $.ajax({
      type: "POST",
      url: "php/project",
      data: {
        "op": "create",
        "name": name,
        "description": description,
        "aoi": aoi
      },
      success: function(result, status, xhr) {
        console.log(status);

        setTimeout(function() {
          $("#loadingModal").modal("hide");
          window.location.reload();
        }, 500);
      },
      error: function(xhr, status, error) {
        console.log(xhr.status);
        console.log(error);
      }
    });
  });


  $("#projectRow #infoProject").click(function(ev) {
    let projectid = $(this).attr("data-projectid");

    $("#loadingModal").modal("show");

    $.ajax({
      type: "GET",
      url: "php/project",
      data: {
        "op": "get_all",
        "pid": projectid
      },
      dataType: "json",
      success: function(result, status, xhr) {
        let data = result;
        let project = data.info;

        let date = new Date(project.created_on);
        $("#infoModal #projectName").html(project.name);
        $("#infoModal #projectDescription").html(project.description);
        $("#infoModal #projectCreatedOn").html(date.toDateString());

        let res = ``;
        for(let u of data.shared) {
          res += `${u.username} &nbsp;&nbsp; <em>${u.email}</em>, <br />`;
        }
        if(res == "") res = "<em>No users shared with</em>";
        $("#infoModal #projectShared").html(res);

        setTimeout(function() {
          $("#loadingModal").modal("hide");

          $("#infoModal").modal("show");
        }, 500);
      },
      error: function(xhr, status, error) {
        console.log(xhr.status);
        console.log(error);
      }
    });
  });


  $("#projectRow #shareProject").click(function(ev) {
    let projectid = $(this).attr("data-projectid");
    $("#shareProjectModal form#shareProjectForm").attr("data-projectid", projectid);
  });

  let doneTypingInterval = 700, typingTimer;
  $("#shareProjectModal input#shareName").on("keyup", function(ev) {
    clearTimeout(typingTimer);
    typingTimer = setTimeout(() => doneTyping(this), doneTypingInterval);
  });
  $("#shareProjectModal input#shareName").on("keydown", (ev) => clearTimeout(typingTimer));

  function doneTyping(self) {
    let term = $(self).val();

    if(term == "") return;

    $.ajax({
      type: "GET",
      url: "php/search",
      data: {
        "op": "user",
        "term": term
      },
      dataType: "json",
      success: function(result, status, xhr) {
        let data = result, res;

        res = ``;
        for(let u of data) {
          let date = new Date(u.created_on);
          res += `
            <button
              type=\"submit\"
              class=\"list-group-item list-group-item-action\"
              data-sharetype=\"user\"
              data-shareid=\"${u.userid}\"
            >
              <div class=\"d-flex w-100 justify-content-between\">
                <h5 class=\"mb-1\">${u.username}</h5>
                <small>${date.toDateString()}</small>
              </div>
              <p class=\"mb-1\">${u.email}</p>
            </button>
          `;
        }

        if(res !== "")
          $("#shareProjectModal #userList").html(res);
        else
          $("#shareProjectModal #userList").html(`
            <button type=\"button\" class=\"list-group-item list-group-item-action\" disabled>No entities found</button>
          `);
      },
      error: function(xhr, status, error) {
        console.log(xhr.status);
        console.log(error);
      }
    });
  }

  $("#shareProjectModal form#shareProjectForm").submit(function(ev) {
    ev.preventDefault();

    $("#shareProjectModal").modal("hide");

    let btn = ev.originalEvent.submitter;

    let projectid = $(this).attr("data-projectid"),
        shareType = $(btn).attr("data-sharetype"),
        shareId = $(btn).attr("data-shareid");

    $("#loadingModal").modal("show");

    $.ajax({
      type: "POST",
      url: "php/project",
      data: {
        "op": "share",
        "pid": projectid,
        "shareId": shareId,
        "shareType": shareType
      },
      success: function(result, status, xhr) {
        console.log(status);

        setTimeout(function() {
          $("#loadingModal").modal("hide");
        }, 500);
      },
      error: function(xhr, status, error) {
        console.log(xhr.status);
        console.log(error);
      }
    });
  });


  $("#projectRow #editProject").click(function(ev) {
    let projectid = $(this).attr("data-projectid");
    $("#editProjectModal button#editProject").attr("data-projectid", projectid);

    $.ajax({
      type: "GET",
      url: "php/project",
      data: {
        "op": "get",
        "pid": projectid
      },
      dataType: "json",
      success: function(result, status, xhr) {
        let data = result;
        let aoi = JSON.parse(data.aoi);

        $("#editProjectModal input#projectName").val(data.name);
        $("#editProjectModal textarea#projectDescription").val(data.description);
        emap.setView([aoi.latitude, aoi.longitude], aoi.zoom);
      },
      error: function(xhr, status, error) {
        console.log(xhr.status);
        console.log(error);
      }
    });
  });

  $("#editProjectModal form#editProjectForm").submit(function(ev) {
    ev.preventDefault();

    $("#editProjectModal").modal("hide");

    let projectid = $("#editProjectModal button#editProject").attr("data-projectid"),
        name = $("#editProjectModal input#projectName").val(),
        description = $("#editProjectModal textarea#projectDescription").val(),
        center = emap.getCenter(),
        zoom = emap.getZoom();
    let aoi = {
          latitude: center.lat,
          longitude: center.lng,
          zoom: zoom
        };

    $("#loadingModal").modal("show");

    $.ajax({
      type: "POST",
      url: "php/project",
      data: {
        "op": "update",
        "pid": projectid,
        "name": name,
        "description": description,
        "aoi": aoi
      },
      success: function(result, status, xhr) {
        console.log(status);

        setTimeout(function() {
          $("#loadingModal").modal("hide");
          window.location.reload();
        }, 500);
      },
      error: function(xhr, status, error) {
        console.log(xhr.status);
        console.log(error);
      }
    });
  });


  $("#projectRow #deleteProject").click(function(ev) {
    let projectid = $(this).attr("data-projectid");
    $("#deleteProjectModal button#deleteProject").attr("data-projectid", projectid);
  });

  $("#deleteProjectModal button#deleteProject").click(function(ev) {
    let projectid = $(this).attr("data-projectid");

    $("#loadingModal").modal("show");

    $.ajax({
      type: "POST",
      url: "php/project",
      data: {
        "op": "delete",
        "pid": projectid
      },
      success: function(result, status, xhr) {
        console.log(status);

        setTimeout(function() {
          $("#loadingModal").modal("hide");
          window.location.reload();
        }, 500);
      },
      error: function(xhr, status, error) {
        console.log(xhr.status);
        console.log(error);
      }
    });
  });
});
