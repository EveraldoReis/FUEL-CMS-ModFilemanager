/*
  RoxyFileman - web based file manager. Ready to use with CKEditor, TinyMCE. 
  Can be easily integrated with any other WYSIWYG editor or CMS.

  Copyright (C) 2013, RoxyFileman.com - Lyubomir Arsov. All rights reserved.
  For licensing, see LICENSE.txt or http://RoxyFileman.com/license

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.

  Contact: Lyubomir Arsov, liubo (at) web-lobby.com
*/
function FileSelected(file){
  /**
   * file is an object containing following properties:
   * 
   * fullPath - path to the file - absolute from your site root
   * path - directory in which the file is located - absolute from your site root
   * size - size of the file in bytes
   * time - timestamo of last modification
   * name - file name
   * ext - file extension
   * width - if the file is image, this will be the width of the original image, 0 otherwise
   * height - if the file is image, this will be the height of the original image, 0 otherwise
   * 
   */
  window.parent.console.log(RoxyUtils.GetUrlParam('img'));
  var s = (file.ext == 'png' || file.ext == 'gif' || file.ext == 'jpg' || file.ext == 'jpeg' || file.ext == 'svg');
  file.fullPath = s ? file.fullPath : jqx_config.basePath + 'fuel/modules/fileman/views/assets/images/filetypes/big/file_extension_' + file.ext + '.png';
  // Set the value of field sent to Fileman via URL param "field".
  window.parent.document.getElementById(RoxyUtils.GetUrlParam('field')).value = file.fullPath;
  // Set the source of an image which id is sent to Fileman via URL param "img".
  window.parent.document.getElementById(RoxyUtils.GetUrlParam('img')).src = file.fullPath;
  // Close file manager if it's opened in separate window. 
  self.close();
  // Close file manager if it's opened in JQuery dialog.
  window.parent.closeCustomRoxy();
}
function GetSelectedValue(){
  /**
  * This function is called to retrieve selected value when custom integration is used.
  * Url parameter selected will override this value.
  */
  
  return "";
}
