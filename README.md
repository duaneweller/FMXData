# FMXData

**A PHP library for the FileMaker Data API**


EXAMPLE CODE AND DOCUMENTATION IS IN THE [PDF MANUAL](manual.pdf)


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


**Enable PHP**

If you have not already enabled PHP for the Custom Web Publishing engine you'll need to open the command line tool (Windows) or Terminal on Mac and enter in this command. Do not "sudo" this on Mac, just type this at the prompt and tap the enter key...

```
fmsadmin set cwpconfig enablephp=true
```

You'll need to enter your FileMaker Server admin console username and password, then restart the FileMaker Server for the changes to take effect.

## INSTALLING THE EXAMPLE FILES

1. Host the FMXData.fmp12 with FileMaker Server. Use the "Upload to Host" feature in FileMaker Pro Advanced (under "File -> Sharing -> Upload to Host") to upload the FMXData.fmp12 to the server.

Open the FMXData.fmp12 with the following credentials: 

Username: admin
Password: admin

2. If you have not already done so, enable the FileMaker Data API in the FileMaker Server Admin Console. Under the connections area select the "FileMaker Data API" and enable it.

3. If you have not already done so, enable PHP from the command line. Open the command line tool (Windows) or Terminal on Mac and enter in the command below. Do not "sudo" this in Terminal. You'll need to enter your FileMaker Server admin console username and password, then restart the FileMaker Server for the changes to take effect.

```
fmsadmin set cwpconfig enablephp=true
```

4. Add the example.php, FMXData.php, and photo.jpg files to the FileMaker Server's Web Server root folder.

The web server's root folder will be in the FileMaker Server directory...

Windows: [drive]:\Program Files\FileMaker\FileMaker Server\HTTPServer\conf

macOS: /Library/FileMaker Server/HTTPServer/htdocs 


**Accessing the Example Files from a Browser**

Open a web browser and go to the following URL:

[http://localhost/example.php](http://localhost/example.php)

## FOR FURTHER INFORMATION ABOUT THE FUNCTIONS CHECK OUT THE MANUAL

[manual.pdf](manual.pdf)

