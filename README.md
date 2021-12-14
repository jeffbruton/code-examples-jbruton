# code-examples-jbruton

cca_gated_resource:
  module created for Crestone Capital that provides functionality to gate certain content, 
  requiring approval to access. Gated form appears above content partially blocking view. 
  Gated form submission notifies admin, admin approves/denies, and follow-up email sent to
  original requestor's email. The version I've added to this repo includes my original setup 
  where the email is constructed and sent through module code. The final version of the module 
  used email settings in the webform so the client had ability to edit message and other 
  details through the ui.

tibco_general:
  most of this module was created by other developers for Tibco. Personally, I added the lazy 
  load plugin filter to the code, and updated for Drupal 9. Client had several modules enabled 
  to manipulate media (blazy, lazy, and others), but wasn't getting a simple, automatic way 
  to add the loading=lazy attribute to images added inline in the ckeditor widget. This module 
  worked for client needs, allowed us to disable some of the other modules.

ui_university:
  module created for Urban Insight to provide a sample Class registration for students. 
  Creates a custom content entity for the Class, admin settings form, and registration form. 
  The Class schedule is added to the user account, ajax updated for each selection. This is a
  fairly simple module, but represents everything needed for a custom entity.
