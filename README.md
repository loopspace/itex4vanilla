itex4vanilla
============

iTeX plugin for the Vanilla 2 forums

Instructions
============

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

The output of `itex2MML` is MathML which is not currently allowed through the
`HtmLawed` sanitiser.  A modified version is distributed with this package.
The only modification is to allow MathML elements and attributes through.
This needs to put installed in place of the `HtmLawed` plugin that comes with
vanilla and which is in `plugins/HtmLawed`.

Usage
=====

A list of the `itex` commands can be found on [Jacques Distler's
website](https://golem.ph.utexas.edu/~distler/blog/itex2MMLcommands.html).
These generally take priority over Markdown commands, except when they are
placed in code blocks.

For browsers that cannot cope with MathML, this plugin also adds a link to the
MathJaX javascript library which will convert the MathML into HTML+CSS.  For
browsers that do understand MathML, this does nothing.  This provides the best
experience in terms of both speed and compatibility.
