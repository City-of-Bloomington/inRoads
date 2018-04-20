inRoads
=======

inRoads is a digital service developed by the City of Bloomington. inRoads is used to manage and publish road, sidewalk and parking status information including closings, lane reductions, noise permits and parking reservations. It provides not only current information about road events, but future planned events as well.inRoads streamlines the process of creating and managing road, sidewalk, parking and other location-relevant municipal events (noise permits, etc.). inRoads publishes its data to the web, to email lists and through multiple open data formats. It also provides a Waze feed usable for communities participating in the Waze Connected Cities program.

The service is provided through an open source PHP application that stores road event data using Google's Calendar API and notifies subscribers via email. The Open Layers (http://openlayers.org/) library provides map rendering functionality. The inRoads interface is built on top of a theming system, allowing other cities and organizations to deploy inRoads in a way that complies with their own visual standards guidelines. This makes setup and installation easier, and also makes it possible to apply updates to the code without losing customizations. CAS and LDAP form the authentication layer, however other solutions specific to other agencies could be integrated with the application.

inRoads is in live production use at https://bloomington.in.gov/inroads

## Requirements
* Linux
* Apache
* MySQL
* PHP

### Dev Requirements
If you want to build this project you'll need
* Linux   - the Makefile is written assuming bash
* gettext - for compiling language files
* pysassc - Python libsass for compiling SASS files

## Installation


We use the same configuration for our PHP applications. To make sure the documentation stays up to date, we maintain it separately. It is available here:

https://github.com/City-of-Bloomington/blossom/wiki
