/**
 *  © UBER INC
 */


/* MapBox token */
const MAPBOX_TOKEN = 'pk.eyJ1IjoiYW5kcmVhc2F0YWthbiIsImEiOiJjazlndzM1cmUwMnl5M21tZjQ3dXpzeHJnIn0.oE5zp040ZzJj5QgCDznweg';

/** STORE **/
const reducers = (function createReducers(redux, keplerGl) {
  return redux.combineReducers({
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
  const initialState = {
    /*keplerGl: {
      map: {
        mapState: {
          latitude: 59.911491,
          longitude: 10.757933
        }
      }
    }*/
  };

  return redux.createStore(
    reducers,
    initialState,
    redux.compose(enhancers)
  );
}(Redux, enhancers));
window.__store = store;
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
      { style: { position: 'absolute', left: 0, width: '100vw', height: '100vh' } },
      react.createElement(keplerGl.KeplerGl, {
        mapboxApiAccessToken: mapboxToken,
        id: 'map',
        width: windowDimension.width,
        height: windowDimension.height,
        appName: 'Napkin Visual'
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
