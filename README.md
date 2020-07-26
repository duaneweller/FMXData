# FMXData

A PHP library for the FileMaker Data API

WHAT IS FMXDATA?

FMXData is a PHP class that provides a library of simple functions for
working with the FileMaker Server Data API. The PHP class takes care of
creating the appropriate cURL calls to connect and run queries through
the FileMaker Server Data API.

WHY FMXDATA?

Intended to be a different flavor of PHP class, FMXData is structured a
little bit differently then some of the other PHP classes for the
FileMaker Sever Data API. As a single file it offers a simple way to add
a library of PHP functions to easily work with the FileMaker Sever Data
API. Functions such as connecting, disconnecting, and uploading
container data are static functions and do not require you to initialize
a class instance to use. Yet, you can initialize separate instances for
each query. This makes it much more flexible to use in PHP framework,
CMS, or even just a simple PHP web page.

