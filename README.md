# link_ex
Extend Drupal Link widdget and allows to set additional attributes on link.
 - Allows to add custom attributes to <a> element for id, class, name, target, rel, accesskey
 - Enables link to set for existing files link using IMCE file browser.
 - Added support for HTML5 download attribute and link formatter.

## Requirements

Works with Durpal 8 and Link


## Known Issue
 - Support IMCE file browser only, if available.
 - Not validating IMCE module dependency 
 - Private managed files information rendered without security check.

## Credits
 - Link attributes (link_attributes)



## Description

![LinkEx](https://user-images.githubusercontent.com/39402077/40540850-8327d6fa-6011-11e8-899c-40bca0dd96a3.png "LinkEx and Imce file manager for link")

- Helpful to use link field for internal / external linking.
- IMCE file manager private file browser and set link for private Drupal managed files. 
- Allow to set custom attributes to link url for batter control over link markup.
- Enable to add file size value in title for Drupal 8 public files.
- Allow to add file extension value in title and link text field.
- Link text field now enable support for file size and file type extension value by using variables as &lt;extension&gt;, &lt;size&gt; or &lt;filename&gt;
- Enables browsing of existing documents or files using Imce file manager and set as link.

## Installation

Install as you would normally install a contributed Drupal module. See:
https://www.drupal.org/docs/8/extending-drupal-8/installing-drupal-8-modules
for further information.

## Resources

- Project page: https://github.com/iknowlex/link_ex
- Visit: www.iknowlex.com

## Update history

- 02 Aug : Drupal private files support and css-class with file extension or mime.
- 23 Jul : Added support for link file size for public files
- 24 May : Initial development release