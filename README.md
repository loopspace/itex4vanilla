itex4vanilla
============

Mathematics plugin for the Vanilla 2 forums

Introduction
============

This plugin adds an extension to the Markdown input formatter for the
Vanilla 2 forums which allows mathematics to be used in forum posts.
The mathematics can either be converted server-side to MathML or left
as-is for client-side processing.

Server-side conversion is handled by the `itex2MML` PHP extension.
Client-side conversion via MathJaX.

Installing itex2MML
===================

To compile the itex2MML PHP extension:

1. Download itex2MML from [Jacques Distler's
website](https://golem.ph.utexas.edu/~distler/blog/itex2MML.html) and extract
the archive somewhere sensible.
2. Copy the `Makefile` from `itex/Makefile` into the `itex-src` directory.
3. Compile the PHP extension using `make php`.  This uses `swig` as well as
   other standard compilation tools.
4. If you have root access, you can install the extension system-wide via
   `make install_php`.  If you are on, for example, shared hosting then you
will need to find out where to put the extension.
5. To make it usable, add the line `extension=itex2MML.so` to your `php.ini`
   file.  If it isn't in a standard location, use the full path.

To make the itex2MML PHP extension available to the Vanilla plugin, copy the
file `itex2MML.php` which `swig` created into the directory
`vendors/markdown/`.

Usage with itex
==========

A list of the `itex` commands can be found on [Jacques Distler's
website](https://golem.ph.utexas.edu/~distler/blog/itex2MMLcommands.html).
These generally take priority over Markdown commands, except when they are
placed in code blocks.

For browsers that cannot cope with MathML, this plugin also adds a link to the
MathJaX javascript library which will convert the MathML into HTML+CSS.  For
browsers that do understand MathML, this does nothing.  This provides the best
experience in terms of both speed and compatibility.

Usage without itex
============

The plugin detects whether or not the `itex` extension is present and
if not, the pieces of mathematics are passed through to the browser
_as-is_ for client-side conversion using MathJaX.  In this situation,
the key purpose of this plugin is to ensure that the mathematics is
passed through without being further processed by Markdown.  In
particular, characters special to Markdown are not processed; these
include underscores, stars, and backslashes.

Sanitisation
======

The in-built HTML sanitiser in Vanilla cannot cope with MathML.
Therefore, if MathML is produced server-side this plugin also provides
an alternate sanitiser which is used in place of the in-built one.
This requires turning off the in-built one which this plugin attempts
to do.  If that is not successful, it needs to be manually switched
off by editing the `config.php` file.
