"use strict";

Drupal.behaviors.appCredentialProductList = {
  isSetUp: false,
  attach: function (context, drupalSettings) {
    if (this.isSetUp) {
      return;
    }

    var rows = document.querySelectorAll('.api-product-list-row');
    if (rows.length < drupalSettings.numOfVisibleRows) {
      return;
    }

    var hidden = 'app-credential-product-list--hidden';

    // NodeList to Array.
    var rowsToHide = Array.prototype.slice.call(rows).slice(drupalSettings.numOfVisibleRows);
    rowsToHide.forEach(function (e) {
      e.classList.add(hidden);
    });

    var showMore = document.getElementById('app-cred-prod-list-show-more');
    var showLess = document.getElementById('app-cred-prod-list-show-less');
    showMore.addEventListener('click', function () {
      rowsToHide.forEach(function (e) {
        e.classList.remove(hidden);
      });
      this.classList.add(hidden);
      showLess.classList.remove(hidden);
    });
    showLess.addEventListener('click', function () {
      rowsToHide.forEach(function (e) {
        e.classList.add(hidden);
      });
      this.classList.add(hidden);
      showMore.classList.remove(hidden);
    });

    this.isSetUp = true;
  }
};
