# FUEL-CMS-ModFilemanager

Module to integrate File Managers in Fuel CMS admin.

Actually support only Roxy File Manager - <a href="http://www.roxyfileman.com/">http://www.roxyfileman.com</a>

###Features
- Generate filemanager area displayed as menu item
- Provide "fileman" custom field to use for replace default image field

##Installation

First clone repository to fuel/modules/fileman folder: 

git clone git@bitbucket.org:everaldoreis/fuel-cms-modfileman.git fileman

Open fuel/application/config/custom_fields.php and add code below:

```

$fields['fileman'] = array(<br/>
'class' => array(FILEMAN_FOLDER => 'Fileman_custom_fields'),<br/>
'function' => 'fileman',<br/>
'filepath' => '',<br/>
'js' => array(<br/>
FUEL_FOLDER => array(),<br/>
),<br/>
'js_function' => '',<br/>
'represents' => array('image'),<br/>
);<br/>

```

On your model file (modelname_model.php) add this code inside form_fields method:

$fields['image']['type'] = 'fileman';

This will replace default file input.

###Options

- preview_width - image width for preview image generated at file select.
- filetype - extension to filter files on list view
