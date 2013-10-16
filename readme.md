# Simple Poll Engine

A really simple poll engine designed to allow extensive integration with existing codebase.

## A) Getting Started

To get started, clone this repository to a directory desired. This readme file 
will assume that you clone to a directory called poll/.

    git clone https://github.com/TheOnly92/Simple-Poll-Engine.git poll

## B) File Structure

I've done my best to reduce the number of files as possible, the following will
be a brief description of what the files are representing under the poll/
directory.

poll
 - external         This folder includes all files needed to access the polls
                    from an external script.
    + include.php   Include this script to use the poll. More on how later.
 - infrastructure   This folder includes code to deal with databases.
 - interfaces       This folder includes definitions on how the code deals with
                    external datastore.
 - static           This folder includes all the static files, including css, js
                    and required images.
 - views            This folder includes the template for the polls.
 + config.php       Put your database credentials in this file, you can also
                    configure external admin database here.
 + domain.php       This file defines the domain of this script.
 + index.php        Provides basic poll functions (list 5 recent polls and basic
                    administrative interface)
 + init.php         This file will initialize the poll script.
 + usecases.php     Most of the business logic is included in this file.

## C) Installation

Generally just upload all the files under the poll/ directory to your public
web root. Edit config.php to suit your needs (database server, name, etc). Also
change the admin password in the config.php if you plan to use the default admin
authentication provider.

You can also choose to provide your own admin authentication provider in case 
you have your own web application, you can configure the variable ``$config['admin']['interactor']``
to your interactor class which must implement the interface AdminInteractorInterface.
More explanation on that on later section.

## D) Access Admin Panel

Once you have uploaded the poll/ directory to your public web root, point your
browser to the specific directory (e.g. http://localhost/poll/). The admin panel
URL will be http://localhost/poll/?c=admin&a=login, change the URL according to
your situation. If you've modified the admin configuration at section B, use 
your own credentials to login, otherwise the default will be admin for username
and admin for password.

Here you can create new polls, modify them, delete, close and view their results.

## E) Integrating Polls

To integrate polls into your php script, add the following line to the desired
place:

    require_once('path to this file include.php'); echo SPE_Poll(poll ID, URL to the poll directory)

Now, you can optionally provide a parameter to the SPE_Poll function for your 
user ID interactor. More on this next section.

## F) Integrating with Your Web App

There are 2 aspects you can integrate, the user part and the admin part. Let's 
talk about the admin part first.

You can provide an admin class which implements the following interface:

    interface AdminInteractorInterface {
        // Checks if authenticated for the admin interface, returns true if yes, false if no
        public function IsAuthenticated();

        // Authenticates with $username and $password, returns true if successful, false if failure
        public function Authenticate($username, $password);

        // Logs out the admin
        public function Unauthenticate();
    }

You can optionally choose not to implement anything for ``Authenticate()`` and
``Unauthenticate()`` (i.e. leaving the function to return void) if you want to 
check authentication against your own $_SESSION.

Next, you can provide your own user interactor so that you can limit votes to
your web app's user IDs. The user interactor must implement the following 
interface:

    interface UserRepository {
        // Returns the identifier (username or user ID), retrieve it through $_SESSION, $_COOKIE or whatsoever
        public function GetIdentifier();
    }

And then you can pass the user interactor as the 3rd parameter to the SPE_Poll
function and configure your poll to limit repeating votes through user IDs.
