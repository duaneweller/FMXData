# FMXData

**A PHP library for the FileMaker Data API**


EXAMPLE CODE AND DOCUMENTATION IS IN THE [PDF MANUAL](fmxData-doc-031.pdf)


## WHAT IS FMXDATA?

FMXData is a PHP class that provides a library of simple functions for working with the FileMaker Server Data API. The PHP class takes care of creating the appropriate cURL calls to connect and run queries through the Data API.

## WHY FMXDATA?

Intended to be a different flavor of PHP class, FMXData is structured a little bit differently then some of the other PHP classes for the Data API. As a single file it offers a simple way to add a library of PHP functions to easily work with the Data API. Functions such as connecting, disconnecting, and uploading container data are static functions and do not require you to initialize a class instance to use. Yet, you can initialize separate instances for each query. This makes it much more flexible to use in PHP framework, CMS, or even just on a simple PHP web page.

## SUPPORTED DATA API FEATURES

fmxData supports all of the following Data API features.

- Log in to a database session
- Log out of a database session
- Get Product Information
- Get Database Names
- Get Script Names
- Get Layout Names
- Get Layout Metadata
- Create a Record
- Edit a Record
- Delete a Record
- Get a Record
- Get a Range of Records
- Upload a File to a Container Field
- Download a Container Field File
- Find FileMaker Records
- Set FileMaker Globals
- Set a Response Layout
- Perform a Script
- Perform Pre-request Script
- Perform Presort Script
- Limit which Portals to Return
- Set Portal Limits and Offsets
- Set the Record Limit and Offset
- Add a Sort Field, Sort Direction, and Sort Order

## THINGS NOT SUPPORTED

fmxData does not yet support the following FileMaker Data API features.

- Log in to an external data source
- Log in to a database session using an OAuth identity provider
- Log in to a database session using a FileMaker ID account
- Duplicate a record
- Logging or Debugging
- Checks or traps for FileMaker errors

The log in features were not necessarily needed for what I plan to use fmxData for but may be added in some future version. Record duplication does not appear to be working as documented in the FileMaker Data API documentation. If at some point Claris updates the documentation, perhaps additional features and functions may be added to fmxData. To keep this class lightweight I have not implemented any kind of logging or debugging. Some of the other PHP classes available offer those features. FMXData does not check or test for errors returned by the FileMaker Server Data API. The entire response is retuned to the calling script so that you can manage the FileMaker errors in your own PHP.

## PREPARING FILEMAKER SERVER

**Enable the FileMaker Data API**

Go into the FileMaker Server Admin Console, under the connections area select the "FileMaker Data API" and enable it.


**Hosting your PHP files**

Since the PHP option was deprecated I've moved to a development environment that includes two computers, one to host FileMaker Server and the other to host my web server. This allows me to maintain a stock install of FileMaker Server that doesn't require configuration beyond the FileMaker Server Console. I run FileMaker Server by itself and run the web server on my development workstation. You will need to have a web server separate from the FileMaker Server to host your PHP files. Although I've never tried it, I don't see why you couldn't run FileMaker server in some sort of Virtual Machine (VM) on the same computer as your web server.

## INSTALLING THE EXAMPLE FILES

1. Host the FMXData.fmp12 with FileMaker Server. Use the "Upload to Host" feature in FileMaker Pro Advanced (under "File -> Sharing -> Upload to Host") to upload the FMXData.fmp12 to the server.

Open the FMXData.fmp12 with the following credentials: 

Username: admin
Password: admin

2. If you have not already done so, enable the FileMaker Data API in the FileMaker Server Admin Console. Under the connections area select the "FileMaker Data API" and enable it.

3. Set up and configure a web server on a separate computer from FileMaker Server. Install your web server of choice and PHP. 

4. Add the example.php, FMXData.php, and photo.jpg files to the web server's root folder.
The web server's root folder will depend on server you choose. Consult your server's documentation for the location.

**Accessing the Example Files from a Browser**

Open a web browser and go to the following URL:

http://[localhost or web servers ip]/example.php


## FOR FURTHER INFORMATION ABOUT THE FUNCTIONS CHECK OUT THE MANUAL

[fmxData-doc-031.pdf](fmxData-doc-031.pdf)

