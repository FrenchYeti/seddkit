# SEDDkit
The official Security Exception Driven Development kit.

## What is SEDD ?
It's a strange idea : detect weaknesses during the development (when testing or executing the script) and throw a special type of  exception targeting the developper.

The exception message contains informations and links to ressources necessary to implement security best practices.
Like the Test Driven Development (TDD) method, this can be called a SecurityException Driven Development (SEDD).

### Why catch these exceptions is useless ?
SEDDkit use several mechanism in order to override native PHP component. Catch the exception can't permit to bypass the control. 
A special features permit to bypass locally a control.

### Can i trace bypass ?
Yes, by searching on your VCS the snippet committed containing the bypass Token, you can be able to identify who accepted the risk.

## How can i use SEDDkit ?
SEDDkit is developped in order to be used with several framworks/CMS, for these reasons, we recommend to use the minified file.
The minified file contains several classes because :
- we not want modify the class autoload
- we not want depend of working directory, directory access restriction, ...

The override of native PHP functions and superglobals can cause side-effect, we greatly encourage you to open a ticket. 

## How work SEDDkit ?
ToDo

