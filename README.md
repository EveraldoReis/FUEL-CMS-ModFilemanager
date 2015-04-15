# FUEL-CMS-ModFilemanager

Module to integrate File Managers in Fuel CMS admin.

Actually support only Roxy File Manager - <a href="http://www.roxyfileman.com/">http://www.roxyfileman.com</a>

<h3>Features</h3>
- Generate filemanager area displayed as menu item
- Provide "fileman" custom field to use for replace default image field

<h2>Installation</h2>

Open fuel/application/config/custom_fields.php and add code below:

$fields['fileman'] = array(<br/>
&nbsp;&nbsp;&nbsp;&nbsp;'class' => array(FILEMAN_FOLDER => 'Fileman_custom_fields'),<br/>
&nbsp;&nbsp;&nbsp;&nbsp;'function' => 'fileman',<br/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'filepath' => '',<br/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'js' => array(<br/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;FUEL_FOLDER => array(),<br/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;),<br/>
&nbsp;&nbsp;&nbsp;&nbsp;'js_function' => '',<br/>
&nbsp;&nbsp;&nbsp;&nbsp;'represents' => array('image'),<br/>
);<br/>

On your model file (modelname_model.php) add this code inside form_fields method:

$fields['image']['type'] = 'fileman';

This will replace default file input.

<h3>Options</h3>

- preview_width - image width for preview image generated at file select.
- filetype - extension to filter files on list view
