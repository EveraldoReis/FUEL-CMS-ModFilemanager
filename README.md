<<<<<<< HEAD
# FUEL-CMS-ModFilemanager

Module to integrate File Managers in Fuel CMS admin.

Actually support only Roxy File Manager - <a href="http://www.roxyfileman.com/">http://www.roxyfileman.com</a>

###Features
- Generate filemanager area displayed as menu item
- Provide "fileman" custom field to use for replace default image field

##Installation

First go to fuel/modules and run: 

git clone https://github.com/EveraldoReis/FUEL-CMS-ModFilemanager.git fileman

Open fuel/application/config/custom_fields.php and add code below:

```

$fields['fileman'] = array(
  'class' => array(FILEMAN_FOLDER => 'Fileman_custom_fields'),
  'function' => 'fileman',
  'filepath' => '',
  'js' => array(
    FUEL_FOLDER => array(),
  ),
  'js_function' => '',
  'represents' => array('image'),
);

```

On your model file (modelname_model.php) add this code inside form_fields method:

$fields['image']['type'] = 'fileman';

This will replace default file input.

###Options

- preview_width - image width for preview image generated at file select.
- filetype - extension to filter files on list view
=======
