/**
 *  © NAPKIN AS
 */

//

/**
 * Customize map.
 * Interact with map store to customize data and behavior
 */
(function customize(keplerGl, store) {
  //store.dispatch(keplerGl.toggleSplitMap());
  let exportMapData = function() { return KeplerGl.KeplerGlSchema.save(store.getState().keplerGl.map); };
}(KeplerGl, store))
