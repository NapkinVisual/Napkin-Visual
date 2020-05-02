/**
 *  © UBER INC
 */


/* Provide MapBox token */
const MAPBOX_TOKEN = 'pk.eyJ1IjoiYW5kcmVhc2F0YWthbiIsImEiOiJjazlndzM1cmUwMnl5M21tZjQ3dXpzeHJnIn0.oE5zp040ZzJj5QgCDznweg';
const WARNING_MESSAGE = 'Please Provide a Mapbox Token';

/* Validate Mapbox Token */
if ((MAPBOX_TOKEN || '') === '' || MAPBOX_TOKEN === 'PROVIDE_MAPBOX_TOKEN') {
  alert(WARNING_MESSAGE);
}

/** STORE **/
const reducers = (function createReducers(redux, keplerGl) {
  return redux.combineReducers({
    // mount keplerGl reducer
    keplerGl: keplerGl.keplerGlReducer
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
const KeplerElement = (function (react, keplerGl, mapboxToken) {
  return function(props) {
    return react.createElement(
      'div',
      {style: {position: 'absolute', left: 0, width: '100vw', height: '100vh'}},
      react.createElement(
        keplerGl.KeplerGl,
        {
          mapboxApiAccessToken: mapboxToken,
          id: 'map',
          width: props.width || window.innerWidth,
          height: props.height || window.innerHeight
        }
      )
    )
  }
}(React, KeplerGl, MAPBOX_TOKEN));

const app = (function createReactReduxProvider(react, reactRedux, KeplerElement) {
  return react.createElement(
    reactRedux.Provider,
    {store},
    react.createElement(KeplerElement, null)
  )
}(React, ReactRedux, KeplerElement));
/** END COMPONENTS **/

/** Render **/
(function render(react, reactDOM, app) {
  reactDOM.render(app, document.getElementById('app'));
}(React, ReactDOM, app));
