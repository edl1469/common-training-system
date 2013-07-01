# Common Training System (CTS)
The CTS is a web application that manages workshop display tasks and registration requests. Each group must have their own application directory, database table, and copy of PHP files.

## Requirements
- There can be any number of organizations running this code-base at the same time, but they all must reside in a common directory and be a sibling of a shared or "common" directory which will hold the page assets.
- An application configuration file, containing all passwords and paths, must be renamed from *_app.ini.php-dist* to the final **_app.ini.php** form.
- HTTP Basic Authentication is used to protect the administrative sub-directory, so two files will need to be renamed here too.
- A MySQL database is required to hold the workshop and user information.
- A special Peoplesoft / *Oracle HR View* is required to gather the user's Primary ASM information.

## Installation
1. If not existing, create a Parent directory off of the host web root.
2. If not existing, create a Shared directory in the Parent and copy all **Common Training Assets (CTA)** project files into it.
3. Create a Group directory in the Parent and copy all **CTS** project files and directories in.
4. Rename the configuration file, by erasing the "-dist" string from the file extension.
5. Replace all configuration string place-holder values with the correct values.
6. Rename the two basic HTTP authentication files, by erasing the "-dist" string from the file name.
7. Follow the instructions to [prepare basic HTTP authentication](http://httpd.apache.org/docs/2.2/programs/htpasswd.html) and user name(s) and password(s) to the *.htpasswd* file.
8. Alter the contents of the *.htaccess* file to better reflect the application identity.

### Example Directory Structure
A parent directory, named *training*, sits on the web root. It holds three sub-directories, *common*, *groupOne*, and *groupTwo*.
The *common* directory holds all of the CSS, image, and Javascript files; as well as the required PHP templating files.
The *groupOne* directory holds all of the PHP files that make the application run, as well as the necessary configuration file.
The *groupTwo* directory is identical to the *groupOne*, with the exception of the configuration file content.