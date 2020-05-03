/**
 *  © NAPKIN AS
 */



function removeLogo() {
  let el = document.querySelector('svg.side-panel-logo__logo');
  if(el) el.remove();
}



function rebrand() {
  let el = document.querySelector('a.logo__link');
  if(el) el.href = 'https://napkingis.no';
}



let firstRun = true;
function bindAddData() {
  let el;

  el = document.querySelector('div.add-data-button');
  if(el) el.addEventListener('click', ev => setTimeout(bindDataModal, 20));

  if(!firstRun) return;
  modalData(); firstRun = false;
}
function bindDataModal() {
  let el = document.querySelector('div.load-data-modal div.load-data-modal__tab>div>div.load-data-modal__tab__item:nth-child(1)');
  if(el) el.addEventListener('click', ev => setTimeout(modalData, 20));

  modalData();
}
function modalData() {
  let el = document.querySelector('div.load-data-modal div.file-uploader div.file-drop-target>div:nth-child(2)>div:nth-child(3)');
  if(el) el.remove();
}



function bindDropDown() {
  let el = document.querySelector('div.save-export-dropdown div#map');
  if(el) el.addEventListener('click', ev => setTimeout(bindExportModal, 20));
}
function bindExportModal() {
  let html = document.querySelector('div.export-map-modal div.selection>div:nth-child(1)'),
      json = document.querySelector('div.export-map-modal div.selection>div:nth-child(2)');
  modalHTML();
  modalJSON();

  html.addEventListener('click', ev => setTimeout(modalHTML, 20));
  json.addEventListener('click', ev => setTimeout(modalJSON, 20));
}
function modalHTML() {
  let el = document.querySelector('div.export-map-modal div.export-map-modal__html-options div.selection div.disclaimer a');
  if(el) el.href = 'https://docs.mapbox.com/help/how-mapbox-works/access-tokens';
}
function modalJSON() {
  let el;

  el = document.querySelector('div.export-map-modal>div>div:nth-child(2)>div');
  if(el) el.remove();

  el = document.querySelector('div.export-map-modal div.export-map-modal__json-options div.description');
  if(el) el.remove();

  el = document.querySelector('div.export-map-modal div.export-map-modal__json-options div.selection div.disclaimer');
  if(el) el.remove();
}



/**
 * Customize map.
 * Interact with map store to customize data and behavior
 */
(function customize(keplerGl, store) {
  //store.dispatch(keplerGl.toggleSplitMap());
  //KeplerGl.KeplerGlSchema.save(store.getState().keplerGl.map);

  removeLogo();
  rebrand();
  bindAddData();
  bindDropDown();

  document.querySelector('div.side-bar__close').addEventListener('click', function(ev) {
    setTimeout(function() {
      removeLogo();
      rebrand();
      bindAddData();
      bindDropDown();
    }, 20);
  });
}(KeplerGl, store))
