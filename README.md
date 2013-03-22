# E100 Compiler

This compiles PHP into E100 Assembly. Written by Loic Sharma

## Installation

You'll need to have PHP installed for this. Obviously. Run the following commands:

`cd /path/to/compiler`
`curl -s http://getcomposer.org/installer | php`
`php composer.phar install`

This will install all of the compiler's dependencies.

## Usage

Once installed, you can compile a PHP script like so:

`./compiler compile <source file> <compiled file>`

For example:

`./compiler compile source.php compiled.e`