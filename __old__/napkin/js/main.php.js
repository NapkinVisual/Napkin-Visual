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


  $.ajax({
    type: "GET",
    url: "php/project",
    //contentType: "application/json",
    data: {
      "op": "get_sizes"
    },
    dataType: "json",
    success: function(result, status, xhr) {
      let data = [], labels = [];
      for(let r of result) {
        labels.push(r.name);
        data.push(r.size);
      }

      let ctx = document.querySelector("#sizeChart");
      let myChart = new Chart(ctx, {
        type: "bar",
        data: {
          labels: labels,
          datasets: [{
            data: data,
            borderColor: "#007bff",
            borderWidth: 1,
            label: "Project size"
          }]
        },
        options: {
          scales: {
            yAxes: [{
              scaleLabel: {
                display: true,
                labelString: "rows"
              },
              ticks: {
                beginAtZero: true
              }
            }]
          }
        }
      });
    },
    error: function(xhr, status, error) {
      console.log(xhr.status);
      console.log(error);
    }
  });
});
