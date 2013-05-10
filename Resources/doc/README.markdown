# CCETCErrorReportBundle - README

*NOTE* - this branch, ``MyCCE`` is for use with that specific project.

Provides an interface for the submission of simple text+e-mail "error reports".
Used on all front and back ends of CCETC web applications.

## Features
A simple error report form with matching SonataAdmin class.  Error Reports consist of email or a user relation, a single block of text, date reported, request->server attribute, and spam and opened booleans for processing.

An e-mail is sent to ``ccetc_error_report.support_email`` every time a report is submitted.

The report form provides a mailto link to this same address with a subject of ``ccetc_error_report.direct_email_subject``.

The bundle includes a simple help page with the form embedded.

The form can be rendered in any template (frontend/backend/error pages), but all submissions routes to the help page.

## Installation
Add to composer:

        "ccetc/error-report-bundle" : "dev-master"

Install assets:

        bin/vendors install

## Config
Add the following lines to your services config.yml:

		ccetc_error_report:
		  support_email: haggertypat@gmail.com
		  direct_email_subject: Template+Error+Report
		  
#### Options
* support_email - email used to error report notices, and for the direct e-mail link on the form
* subject for the direct e-mail link (default: Error Report)

Add the user side of the report/user relation to your user class:

        <one-to-many field="errorReportsSubmitted" target-entity="CCETC\ErrorReportBundle\Entity\ErrorReport" mapped-by="userSubmittedBy" />


## Use
### Help Pages
To include "help pages" define routes:

        adminHelp:
                pattern: /adminHelp
                defaults: { _controller: CCETCErrorReportBundle:ErrorReport:errorReport, usePageHeader: true, flash: sonata_flash_success, redirect: sonata_admin_dashboard, baseLayout: SonataAdminBundle::standard_layout.html.twig, formRoute: adminHelp }

        frontendHelp:
                pattern: /help
                defaults: { _controller: CCETCErrorReportBundle:ErrorReport:errorReport, usePageHeader: false, flash: my_message, redirect: home, baseLayout: "::my_layout.html.twig", formRoute: frontendHelp }


#### Route Options
* usePageHeader: if true, the "Help" heading will be placed in the ``page_header`` block.  Otherwise, the heading will go at the top of the content block.  Default: false
* flash: name of the flash for the success message. Default: message
* redirect: route to redirect to on success. Default: home
* baseLayout: the layout to extend
* formRoute: the route of your help page, used to redirect on errors.  Default: help

*Note*: the base layout that the error report form extends *must* have a block called "stylesheets" and a block called "content"

### Template Embedding
You can emped the form in a template, provied you have a "help" route defined to process it.  If there are errors, the users will be redirected to help page to finish their submission.  To include the form in a template:

        {% render "CCETCErrorReportBundle:ErrorReport:errorReportForm" with {
            'formRoute' : 'help',
            'formText' : 'Please tell us about this error.'
        }%}
        
#### Options
* formRoute: the route of your help page, used to redirect on errors.  Default: help
* formText: the text displayed above the form before the direct e-mail link.  Default: "Having trouble with somthing?


## Documentation
All ISSUES, IDEAS, and FEATURES are documented on the [trello board](https://trello.com/board/errorreportbundle/4f9014c7c9fa68b12a0cdb13).
