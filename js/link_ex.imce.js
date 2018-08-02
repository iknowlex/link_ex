/**
 * @file
 * Contains main module script functions.
 * More info: https://github.com/iknowlex/link_ex
 */
(function ($, Drupal) {
  "use strict";

  /**
   * @file
   * Provides methods for integrating Imce into text fields.
   */

  /**
   * Drupal behavior to handle url input integration.
   */
  Drupal.behaviors.link_exImce = {
    attach: function (context, settings) {
      $("input[data-link_ex-file_browser='imce']", context).not('.imce-url-processed').addClass('imce-url-processed').each(imceInputEx.processUrlInput);
    }
  };
  
  /**
   * Global container for integration helpers.
   */
  var imceInputEx = window.imceInputEx = window.imceInputEx || {

    /**
     * Processes an url input.
     */
    processUrlInput: function(i, el) {
	 var link_exWrap =	document.createElement('div');
	 link_exWrap.className = 'link_ex-wrap';
      var button = imceInputEx.createUrlButton(el.id, el.getAttribute('data-imce-type'), Drupal.t('Open File Manager'));
	  button.classList.add("link_ex-fm");
	  link_exWrap.appendChild(button);
      //el.parentNode.insertBefore(button, el);
	  if( el.hasAttribute('data-link_ex-file_private')) {
         var buttonp = imceInputEx.createUrlButton(el.id, el.getAttribute('data-imce-type'), Drupal.t('Open File Manager (Private)'));
	     buttonp.classList.add("link_ex-fm-private");
	     buttonp.href = '/imce/private';
	     link_exWrap.appendChild(buttonp);
	  }
      el.parentNode.insertBefore(link_exWrap, el);
    },

    /**
     * Creates an url input button.
     */
    createUrlButton: function(inputId, inputType, inputLabel) {
      var button = document.createElement('a');
      button.href = '#';
      button.className = 'link_ex-imce-button';
      button.innerHTML = '<span>' + inputLabel + '</span>';
      button.onclick = imceInputEx.urlButtonClick;
      button.InputId = inputId || 'imce-url-input-' + (Math.random() + '').substr(2);
      button.InputType = inputType || 'link';
      return button;
    },

    /**
     * Click event of an url button.
     */
    urlButtonClick: function(e) {
	var url = Drupal.url('imce'); if (this.getAttribute('href') !== '#') { url = this.getAttribute('href'); }
      url += (url.indexOf('?') === -1 ? '?' : '&') + 'sendto=imceInputEx.urlSendto&inputId=' + this.InputId + '&type=' + this.InputType;
      // Focus on input before opening the window
      $('#' + this.InputId).focus();
      window.open(url, '', 'width=' + Math.min(1000, parseInt(screen.availWidth * 0.8, 10)) + ',height=' + Math.min(800, parseInt(screen.availHeight * 0.8, 10)) + ',resizable=1');
      return false;
    },

    /**
     * Sendto handler for an url input.
     */
    urlSendto: function(File, win) {
      var url = File.getUrl();
      var el = $('#' + win.imce.getQuery('inputId'))[0];
      win.close();
      if (el) {
        $(el).val(decodeURI(url)).change().focus();
      }
    }
  
  };

})(jQuery, Drupal);
