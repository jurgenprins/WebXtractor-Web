WebXtractor-Web is a website frontend for the WebXtractor-PHP library

WebXtractor-Web uses the 
 1) Zend Framework  				(not included in codebase)
   * MVC (controllers, views, services, models incl. data mappers best practice)
   * REST services (JSON based on header accept)
 2) EXT-JS Javascript UI  	(included in codebase)
   * Datadrop & Iframe plugin
 3) MySQL database
 
to implement both a Web/HTML and REST/JSON based service platform to
create, maintain, run and view collections of items that are indexed
and parsed into normalized 'web-items' by the WebXtractor-PHP library

The codebase currently runs at http://www.bokella.com/ but is not yet
production ready. It misses some critical user friendly functions, such
that there is no complete collection management, running and viewing 
experience yet. A demo user is included as 'demo' with password '563412'
