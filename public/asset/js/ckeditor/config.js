/**
 * @license Copyright (c) 2003-2017, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
  config.toolbarGroups = [
    { name: 'document', groups: [ 'mode', 'document', 'doctools' ] },
    { name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
    { name: 'editing', groups: [ 'find', 'selection', 'spellchecker', 'editing' ] },
    { name: 'forms', groups: [ 'forms' ] },
    '/',
    { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
    { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi', 'paragraph' ] },
    { name: 'links', groups: [ 'links' ] },
    { name: 'insert', groups: [ 'insert' ] },
    '/',
    { name: 'styles', groups: [ 'styles' ] },
    { name: 'colors', groups: [ 'colors' ] },
    { name: 'tools', groups: [ 'tools' ] },
    { name: 'about', groups: [ 'about' ] },
    { name: 'others', groups: [ 'others' ] }
  ];

  config.removeButtons = 'Language,Flash,Iframe,About';
  config.allowedContent=true;
  config.font_names = 'Arial;Arial Black;Comic Sans MS;Courier New;Tahoma;Times New Roman;Verdana;新細明體;細明體;標楷體;微軟正黑體';
  config.fontSize_sizes = '10/10px;13/13px;16/16px;18/18px;20/20px;22/22px;24/24px;36/36px;48/48px;';
  config.undoStackSize = 50;
  config.pasteFilter = null;

  // config.contentsCss = ['bootstrap.min.css','font-awesome.min.css']; 
  // CKEDITOR.dtd.$removeEmpty.i = 0; 
  // CKEDITOR.dtd.$removeEmpty.span = 0;
};
