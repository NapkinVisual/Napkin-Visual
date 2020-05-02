/**
 *  © NAPKIN AS
 */


function removeLogo() {
  let el = document.querySelector('svg.side-panel-logo__logo');
  if(el) el.remove();
}

/**
 * Customize map.
 * Interact with map store to customize data and behavior
 */
(function customize(keplerGl, store) {
  // store.dispatch(keplerGl.toggleSplitMap());

  document.querySelector('a.logo__link').href = 'https://napkingis.no';
  removeLogo();

  document.querySelector('div.side-bar__close').addEventListener('click', ev => setTimeout(removeLogo, 20));
}(KeplerGl, store))
