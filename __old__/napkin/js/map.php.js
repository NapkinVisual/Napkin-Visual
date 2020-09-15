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

"use strict";

window.addEventListener("load", function() {
  feather.replace();


  let exportMapData;
  let params = getParams(window.location.href);
  let projectid = params.pid;


  /* MapBox token */
  const MAPBOX_TOKEN = 'pk.eyJ1IjoiYW5kcmVhc2F0YWthbiIsImEiOiJjazlndzM1cmUwMnl5M21tZjQ3dXpzeHJnIn0.oE5zp040ZzJj5QgCDznweg';

  /** STORE **/
  const reducers = (function createReducers(redux, keplerGl) {
    return redux.combineReducers({
      keplerGl: keplerGl.keplerGlReducer.initialState({
        mapState: {
          latitude: 60,
          longitude: 8,
          zoom: 5
        },
        mapStyle: {
          styleType: 'light'
          //threeDBuildingColor: [ 192, 192, 192 ]
        },
        uiState: {
          currentModal: null,
          readOnly: $("#__isViewer__").length > 0
        }
      })
    });
  }(Redux, KeplerGl));

  const middleWares = (function createMiddlewares(keplerGl) {
    return keplerGl.enhanceReduxMiddleware([
      // Add other middlewares here
    ]);
  }(KeplerGl));

  const enhancers = (function craeteEnhancers(redux, middles) {
    return redux.applyMiddleware(...middles);
  }(Redux, middleWares));

  const store = (function createStore(redux, enhancers) {
    const initialState = {};

    return redux.createStore(
      reducers,
      initialState,
      redux.compose(enhancers)
    );
  }(Redux, enhancers));
  /** END STORE **/

  /** COMPONENTS **/
  const KeplerElement = (function(react, keplerGl, mapboxToken) {
    return function(props) {
      let rootElm = react.useRef(null);

      let _useState = react.useState({
        width: window.innerWidth,
        height: window.innerHeight
      });

      let windowDimension = _useState[0],
          setDimension = _useState[1];

      react.useEffect(function sideEffect() {
        function handleResize() {
          setDimension({ width: window.innerWidth, height: window.innerHeight });
        };
        window.addEventListener('resize', handleResize);
        return function() { window.removeEventListener('resize', handleResize); };
      }, []);

      return react.createElement(
        'div',
        { style: { position: 'absolute', left: 0, top: 45, width: '100vw', height: 'calc(100vh - 45px)' } },
        //{ style: { position: 'absolute', left: 0, width: '100vw', height: '100vh' } },
        react.createElement(keplerGl.KeplerGl, {
          mapboxApiAccessToken: mapboxToken,
          id: 'map',
          width: windowDimension.width,
          height: windowDimension.height - 45,
          appName: '',
          appWebsite: 'https://napkingis.no'
        })
      );
    };
  }(React, KeplerGl, MAPBOX_TOKEN));

  const app = (function createReactReduxProvider(react, reactRedux, KeplerElement) {
    return react.createElement(
      reactRedux.Provider,
      { store },
      react.createElement(KeplerElement, null)
    )
  }(React, ReactRedux, KeplerElement));
  /** END COMPONENTS **/

  /** Render **/
  (function render(react, reactDOM, app) {
    reactDOM.render(app, document.getElementById('app'));
  }(React, ReactDOM, app));


  /**
   * Customize map.
   * Interact with map store to customize data and behavior
   */
  (function customize(keplerGl, store) {
    //store.dispatch(keplerGl.toggleSplitMap());
    exportMapData = function() { return KeplerGl.KeplerGlSchema.save(store.getState().keplerGl.map); };


    $.ajax({
      type: "GET",
      url: "php/project",
      data: {
        "op": "get_data",
        "pid": projectid
      },
      dataType: "json",
      success: function(result, status, xhr) {
        let data = result;
        data = JSON.parse(data);
        console.log(data);

        store.dispatch(keplerGl.addDataToMap({
          datasets: data.datasets,
          config: data.config
        }));

        setTimeout(function() {
          $("#loadingAlert").alert("close");
        }, 500);
      },
      error: function(xhr, status, error) {
        console.log(xhr.status);
        console.log(error);
      }
    });
  }(KeplerGl, store));


  $("#save").click(function(ev) {
    let data = exportMapData();

    for(let d of data.datasets) {
      d.info = {
        id: d.data.id,
        label: d.data.label
      };

      d.data = {
        fields: d.data.fields,
        rows: d.data.allData
      };
    }

    $("#alertArea").html(`
      <div class=\"alert alert-info alert-dismissible fade show\" role=\"alert\" id=\"loadingAlert\">
  		  <strong>Saving map...</strong>
  		</div>
    `);

    $.ajax({
      type: "POST",
      url: "php/project",
      data: {
        "op": "save",
        "pid": projectid,
        "data": JSON.stringify(data)
      },
      //dataType: "json",
      success: function(result, status, xhr) {
        console.log(status);

        setTimeout(function() {
          $("#loadingAlert").alert("close");
        }, 500);
      },
      error: function(xhr, status, error) {
        console.log(xhr.status);
        console.log(error);
      }
    });
  });
});



function getParams(url) {
	var params = {};
	var parser = document.createElement('a');
	parser.href = url;
	var query = parser.search.substring(1);
	var vars = query.split('&');
	for (var i = 0; i < vars.length; i++) {
		var pair = vars[i].split('=');
		params[pair[0]] = decodeURIComponent(pair[1]);
	}
	return params;
};
