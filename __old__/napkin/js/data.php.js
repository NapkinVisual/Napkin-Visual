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

let _FILE = null;

window.addEventListener("load", function() {
  feather.replace();
  init();


  $("#createdFrom").datepicker({ orientation: "bottom" });
  $("#createdTo").datepicker({ orientation: "bottom" });


  $("div#upload").click(function(ev) {
    $("input#upload").click();
  });
  $("input#upload").on("change", (ev) => handleFile(ev.target.files[0]));
  $("div#upload").on("dragover dragenter", function(ev) { ev.preventDefault(); ev.stopPropagation(); });
  $("div#upload").on("drop", function(ev) {
    if(ev.originalEvent.dataTransfer
    && ev.originalEvent.dataTransfer.files.length) {
      ev.preventDefault();
      ev.stopPropagation();

      let file = ev.originalEvent.dataTransfer.files[0];

      if(!validFileType(file)) return;

      handleFile(file);
    }
  });

  $("#newDatasourceModal").on("hidden.bs.modal", (ev) => { _FILE = null; });
  $("#editDatasourceModal").on("hidden.bs.modal", (ev) => { _FILE = null; });


  $("#newDatasourceModal form#createDatasourceForm").submit(function(ev) {
    ev.preventDefault();

    $("#newDatasourceModal").modal("hide");

    let type = getFileType(_FILE),
        name = $("#newDatasourceModal input#datasourceName").val(),
        description = $("#newDatasourceModal textarea#datasourceDescription").val();

    $("#loadingModal").modal("show");

    let fd = new FormData();
    fd.append("op", "create");
    fd.append("type", type);
    fd.append("name", name);
    fd.append("description", description);
    fd.append("file", _FILE);

    $.ajax({
      type: "POST",
      url: "php/data",
      data: fd,
      contentType: false,
      processData: false,
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

        _FILE = null;
      }
    });
  });


  $("#datasourceRow #infoDatasource").click(function(ev) {
    let datasourceid = $(this).attr("data-datasourceid");

    $("#loadingModal").modal("show");

    $.ajax({
      type: "GET",
      url: "php/data",
      data: {
        "op": "get",
        "did": datasourceid
      },
      dataType: "json",
      success: function(result, status, xhr) {
        let data = result;
        let owner = data.owner;

        let date = new Date(data.created_on);
        $("#infoModal #datasourceType").html(data.type);
        $("#infoModal #datasourceName").html(data.name);
        $("#infoModal #datasourceDescription").html(data.description);
        $("#infoModal #datasourceCreatedOn").html(date.toDateString());

        $("#infoModal #datasourceFile").html(`
          <a href=\"${window.location.protocol}//${window.location.hostname}/napkin/${data.filepath}\" target=\"_blank\">
            open file
          </a>
        `);

        if($("#infoModal #datasourceOwner").length > 0) {
          $("#infoModal #datasourceOwner").html(`
            ${owner.username} &nbsp;&nbsp; <em>${owner.email}</em>
          `);
        }

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


  $("#datasourceRow #editDatasource").click(function(ev) {
    let datasourceid = $(this).attr("data-datasourceid");
    $("#editDatasourceModal button#editDatasource").attr("data-datasourceid", datasourceid);

    $.ajax({
      type: "GET",
      url: "php/data",
      data: {
        "op": "get",
        "did": datasourceid
      },
      dataType: "json",
      success: function(result, status, xhr) {
        let data = result;

        $("#editDatasourceModal input#datasourceName").val(data.name);
        $("#editDatasourceModal textarea#datasourceDescription").val(data.description);
      },
      error: function(xhr, status, error) {
        console.log(xhr.status);
        console.log(error);
      }
    });
  });

  $("#editDatasourceModal form#editDatasourceForm").submit(function(ev) {
    ev.preventDefault();

    $("#editDatasourceModal").modal("hide");

    let datasourceid = $("#editDatasourceModal button#editDatasource").attr("data-datasourceid"),
        name = $("#editDatasourceModal input#datasourceName").val(),
        description = $("#editDatasourceModal textarea#datasourceDescription").val();

    $("#loadingModal").modal("show");

    let fd = new FormData();
    fd.append("op", "update");
    fd.append("did", datasourceid);
    fd.append("name", name);
    fd.append("description", description);

    if(_FILE) {
      let type = getFileType(_FILE);

      fd.append("type", type);
      fd.append("file", _FILE);
    }

    $.ajax({
      type: "POST",
      url: "php/data",
      data: fd,
      contentType: false,
      processData: false,
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

        _FILE = null;
      }
    });
  });


  $("#datasourceRow #deleteDatasource").click(function(ev) {
    let datasourceid = $(this).attr("data-datasourceid");
    $("#deleteDatasourceModal button#deleteDatasource").attr("data-datasourceid", datasourceid);
  });

  $("#deleteDatasourceModal button#deleteDatasource").click(function(ev) {
    let datasourceid = $(this).attr("data-datasourceid");

    $("#loadingModal").modal("show");

    $.ajax({
      type: "POST",
      url: "php/data",
      data: {
        "op": "delete",
        "did": datasourceid
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






function handleFile(file) {
  _FILE = file;

  $("small#dropzoneCont").html(file.name);
}


function getFileType(file) {
  let filetype = file.type;

  switch(filetype) {
    case "text/csv":
    case "application/csv":
      return "csv";

    case "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet":
    case "application/vnd.ms-excel":
      return "excel";

    case "application/json":
    case "application/geo+json":
      return "geojson";

    case "application/x-esri-shape":
      return "shape";

    default:
      let isCSV = file.name.split(/\./ig).pop().toLowerCase() == "csv";

      if(isCSV) return "csv";
  }
}


function validFileType(file) {
  let type = getFileType(file);

  if(type == "csv"
  || type == "excel"
  || type == "geojson"
  || type == "shape"
  || type == "sap"
  || type == "sap_hana")
    return true;

  return false;
}
