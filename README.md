ErrorReportBundle
=========

Provides an interface for the submission of simple text+e-mail "error reports".
Used on all front and back ends of CCETC web applications.

## Installation
To install as a Symfony vendor, add the following lines to the file ``deps``:

        [ErrorReportBundle]
                git=https://github.com/CCETC/ErrorReportBundle.git
                target=/bundles/CCETC/ErrorReportBundle
      
and run:

        bin/vendors install

If you are using git, you can instead add them as submodules:

        git submodule add git://github.com/CCETC/ErrorReportBundle.git vendor/bundles/CCETC/ErrorReportBundle


Add the following lines to your services config.yml:

        services:
                errorReports:
                class: CCETC\ErrorReportBundle\Resources\config\Config
                arguments: [your@email.com]   


## Use
To add a page with an error report form add a route to routing.yml:

        adminHelp:
                pattern: /adminHelp
                defaults: { _controller: CCETCErrorReportBundle:ErrorReport:errorReport, headingBlock: breadcrumb, flash: sonata_flash_success, redirect: sonata_admin_dashboard, baseLayout: SonataAdminBundle::standard_layout.html.twig, formRoute: adminHelp }

        frontendHelp:
                pattern: /help
                defaults: { _controller: CCETCErrorReportBundle:ErrorReport:errorReport, headingBlock: content, flash: message, redirect: home, baseLayout: ::layout.html.twig, formRoute: frontendHelp }


### Route Options
* headingBlock: the name of block that the heading with the text "Help" and question mark icon should be placed.  Default: content
* flash: name of the flash for the success message. Default: message
* redirect: route to redirect to on success. Default: home
* baseLayout: the layout to extend
* formRoute: the name of this route, used to redirect on errors