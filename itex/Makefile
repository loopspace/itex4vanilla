#YACC=yacc
YACC=bison -y  -v
SWIG=swig
RUBY=ruby
PERL=perl
PHP=php
PYTHON=python
PHP_CONFIG=php-config

RM=rm -f
INSTALL=install -c
BINDIR=$(HOME)/bin

YYPREFIX=itex2MML_yy

# Target overrides
ruby19 : RUBY=ruby19
python : TYPE=num
num : TYPE=num
ynum.% : TYPE=num
lexnum.% : TYPE=num

LEX=flex -P$(YYPREFIX) -olex$(TYPE).yy.c

all:    y$(TYPE).tab.c lex$(TYPE).yy.c itex2MML$(TYPE)

num: all

y$(TYPE).tab.c:	itex2MML$(TYPE).y
		$(YACC) -p $(YYPREFIX) -d itex2MML$(TYPE).y -o y$(TYPE).tab.c

lex$(TYPE).yy.c:	itex2MML$(TYPE).l
		$(LEX) itex2MML$(TYPE).l

y$(TYPE).tab.o:	y$(TYPE).tab.c itex2MML.h
		$(CC) $(CFLAGS) -c -o y$(TYPE).tab.o y$(TYPE).tab.c

lex$(TYPE).yy.o:	lex$(TYPE).yy.c y$(TYPE).tab.c itex2MML.h
		$(CC) $(CFLAGS) -c -o lex$(TYPE).yy.o lex$(TYPE).yy.c

itex2MML$(TYPE):	lex$(TYPE).yy.o y$(TYPE).tab.o itex2MML.cc itex2MML.h
		$(CXX) $(CFLAGS) -o itex2MML$(TYPE) lex$(TYPE).yy.o y$(TYPE).tab.o itex2MML.cc

universal:		
		CFLAGS='-arch i686 -arch ppc' make all

clean:		
		$(RM) ynum.tab.* y.tab.* lex.yy.c lexnum.yy.c itex2MML itex2MMLnum *.o *.output *.so *.dll *.sl *.bundle itex2MML_ruby.c itex2MML_php.c itex2MML_perl.c itex2MML.pm itex2MML.php php_itex2MML.h itex2MML_python.c itex2MML.py

install:	itex2MML
		$(INSTALL) itex2MML $(BINDIR)

RUBY_CFLAGS = $(shell $(RUBY) -e 'require "rbconfig"; print RbConfig::CONFIG["CFLAGS"]')
RUBY_LD = $(shell $(RUBY) -e 'require "rbconfig"; print RbConfig::CONFIG["LDSHARED"]')
RUBYLIBDIR =$(shell $(RUBY) -e 'require "rbconfig"; print RbConfig::CONFIG["libdir"]') 
RUBY_PREFIX =  $(shell $(RUBY) -e 'require "rbconfig"; print RbConfig::CONFIG["rubylibdir"]')
RUBY_ARCH = $(shell $(RUBY) -e 'require "rbconfig"; print RbConfig::CONFIG["arch"]')
RUBYDIR = $(shell $(RUBY) -e 'require "rbconfig"; print RbConfig::CONFIG["archdir"]')
RUBYHDRDIR = $(shell $(RUBY) -e 'require "rbconfig"; print RbConfig::CONFIG["rubyhdrdir"]')
RUBY_SITEDIR = $(shell $(RUBY) -e 'require "rbconfig"; print RbConfig::CONFIG["sitelibdir"]')
RUBY_SITEARCHDIR = $(shell $(RUBY) -e 'require "rbconfig"; print RbConfig::CONFIG["sitearchdir"]')
LIBRUBYARG = $(shell $(RUBY) -e 'require "rbconfig"; print RbConfig::CONFIG["LIBRUBYARG"]')
DYLIB_EXT = $(shell $(RUBY) -e 'require "rbconfig"; print RbConfig::CONFIG["DLEXT"]')
RUBY_target_os = $(shell $(RUBY) -e 'require "rbconfig"; print RbConfig::CONFIG["target_os"]')

ifneq (, $(findstring darwin, $(RUBY_target_os)) )
EXTRA_CFLAGS = -DHAVE_SNPRINTF -DHAVE_STRUCT_TIMESPEC -DHAVE_STRUCT_TIMEZONE
LD_TARGET = MACOSX_DEPLOYMENT_TARGET=10.4
else
EXTRA_CFLAGS = ''
LD_TARGET = ''
endif

itex2MML_ruby.c:	itex2MML.i
			$(SWIG) -ruby -o itex2MML_ruby.c  itex2MML.i

itex2MML_ruby.o:	itex2MML_ruby.c
			$(CC) $(RUBY_CFLAGS)  $(EXTRA_CFLAGS) -c itex2MML_ruby.c $(RUBYIDIR) -o itex2MML_ruby.o

y.tab_ruby.o:		y.tab.c
			$(CC) $(RUBY_CFLAGS) -Ditex2MML_CAPTURE -c -o y.tab_ruby.o y.tab.c

lex.yy_ruby.o:	lex.yy.c y.tab.c itex2MML.h
		$(CC) $(RUBY_CFLAGS) -c -o lex.yy_ruby.o lex.yy.c

ruby:	itex2MML_ruby.o y.tab_ruby.o lex.yy_ruby.o
	$(LD_TARGET) $(RUBY_LD) itex2MML_ruby.o y.tab_ruby.o lex.yy_ruby.o -L$(RUBYLIBDIR) $(LIBRUBYARG) -o itex2MML.$(RUBY_DYLIB_EXT)

ruby19: ruby

test_ruby:
	$(RUBY) ./itextomml.rb

install_ruby:	itex2MML.$(RUBY_DYLIB_EXT)
	$(INSTALL) itex2MML.$(RUBY_DYLIB_EXT) $(RUBY_SITEARCHDIR)
	$(INSTALL) itextomml.rb itex_stringsupport.rb $(RUBY_SITEDIR)

PHP_CFLAGS=-g -O2 -fPIC $(shell $(PHP_CONFIG) --includes)
PHP_SITEARCHDIR = $(shell $(PHP_CONFIG) --extension-dir)
PHPDIR=$(shell $(PHP_CONFIG) --include-dir)
PHP_CC=gcc
PHP_LD=gcc -shared -Wl,-undefined,dynamic_lookup
PHP_SITEDIR=$(shell $(PHP_CONFIG) --include-dir)
PHP_DLEXT=so

itex2MML_php.c:	itex2MML.i
			swig -php5 -o itex2MML_php.c  itex2MML.i

itex2MML_php.o:	itex2MML_php.c
			$(PHP_CC) $(PHP_CFLAGS) -c itex2MML_php.c -I$(PHPDIR) -o itex2MML_php.o

y$(TYPE).tab_php.o:		y$(TYPE).tab.c
			$(PHP_CC) $(PHP_CFLAGS) -Ditex2MML_CAPTURE -c -o y$(TYPE).tab_php.o y$(TYPE).tab.c

lex$(TYPE).yy_php.o:	lex$(TYPE).yy.c y$(TYPE).tab.c itex2MML.h
		$(PHP_CC) $(PHP_CFLAGS) -c -o lex$(TYPE).yy_php.o lex$(TYPE).yy.c

php:	itex2MML_php.o y$(TYPE).tab_php.o lex$(TYPE).yy_php.o
	$(PHP_LD) itex2MML_php.o y$(TYPE).tab_php.o lex$(TYPE).yy_php.o  $(LIBPHPARG) -o itex2MML.$(PHP_DLEXT)

test_php:
	$(PHP) ./itextomml.php

install_php:	itex2MML.$(PHP_DLEXT)
	$(INSTALL) itex2MML.$(PHP_DLEXT) $(PHP_SITEARCHDIR)
	$(INSTALL) itex2MML.php $(PHP_SITEDIR)
	$(INSTALL) itextomml.php $(PHP_SITEDIR)

PERL_CFLAGS=$(shell $(PERL) -e 'use Config; print $$Config{ccflags}. " -fPIC"')
PERL_SITEARCHDIR =$(shell $(PERL) -e 'use Config; print $$Config{sitearch}')
PERLDIR=$(shell $(PERL) -e 'use Config; print $$Config{archlib} . "/CORE"')
PERL_LD=$(shell $(PERL) -e 'use Config; print $$Config{ld} ." " . $$Config{lddlflags}')
PERL_SITEDIR=$(shell $(PERL) -e 'use Config; print $$Config{installsitelib}')
PERL_DLEXT=$(shell $(PERL) -e 'use Config; print $$Config{dlext}')

itex2MML_perl.c:	itex2MML.i
			swig -perl5 -o itex2MML_perl.c  itex2MML.i

itex2MML_perl.o:	itex2MML_perl.c
			$(CC) $(PERL_CFLAGS) -c itex2MML_perl.c -I$(PERLDIR) -o itex2MML_perl.o

y.tab_perl.o:		y.tab.c
			$(CC) $(PERL_CFLAGS) -Ditex2MML_CAPTURE -c -o y.tab_perl.o y.tab.c

lex.yy_perl.o:	lex.yy.c y.tab.c itex2MML.h
		$(CC) $(PERL_CFLAGS) -c -o lex.yy_perl.o lex.yy.c

perl:	itex2MML_perl.o y.tab_perl.o lex.yy_perl.o
	$(PERL_LD) itex2MML_perl.o y.tab_perl.o lex.yy_perl.o  $(LIBPERLARG) -o itex2MML.$(PERL_DLEXT)

test_perl:
	$(PERL) ./ItexToMML.pm

install_perl:	itex2MML.$(PERL_DLEXT)
	$(INSTALL) itex2MML.$(PERL_DLEXT) $(PERL_SITEARCHDIR)
	$(INSTALL) itex2MML.pm $(PERL_SITEDIR)
	$(INSTALL) ItexToMML.pm $(PERL_SITEDIR)

PYTHON_CFLAGS=$(shell $(PYTHON) -c 'import distutils.sysconfig; print distutils.sysconfig.get_config_var("CFLAGS");') $(shell $(PYTHON) -c 'import distutils.sysconfig; print distutils.sysconfig.get_config_var("CFLAGSFORSHARED");')
PYTHONDIR=$(shell $(PYTHON) -c 'import distutils.sysconfig; print distutils.sysconfig.get_config_var("INCLUDEPY");')
PYTHON_LD=$(shell $(PYTHON) -c 'import distutils.sysconfig; print distutils.sysconfig.get_config_var("LDSHARED");')
PYTHON_DLEXT=$(shell $(PYTHON) -c 'import distutils.sysconfig; print distutils.sysconfig.get_config_var("SHLIB_EXT");')
PYTHON_SITEDIR=$(shell $(PYTHON) -c 'import distutils.sysconfig; print distutils.sysconfig.get_python_lib(1,0);')
LIBPYTHONARG=$(shell $(PYTHON) -c 'import distutils.sysconfig; print distutils.sysconfig.get_config_var("BLDLIBRARY");') -L$(shell $(PYTHON) -c 'import distutils.sysconfig; print distutils.sysconfig.get_config_var("LIBDIR");')

itex2MML_python.c:	itex2MML.i
			swig -python -o itex2MML_python.c  itex2MML.i

itex2MML_python.o:	itex2MML_python.c
			$(CC) $(PYTHON_CFLAGS) -c itex2MML_python.c -I$(PYTHONDIR) -o itex2MML_python.o

y$(TYPE).tab_python.o:		y$(TYPE).tab.c
			$(CC) $(PYTHON_CFLAGS) -Ditex2MML_CAPTURE -c -o y$(TYPE).tab_python.o y$(TYPE).tab.c

lex$(TYPE).yy_python.o:	lex$(TYPE).yy.c y$(TYPE).tab.c itex2MML.h
		$(CC) $(PYTHON_CFLAGS) -c -o lex$(TYPE).yy_python.o lex$(TYPE).yy.c

python:	itex2MML_python.o y$(TYPE).tab_python.o lex$(TYPE).yy_python.o
	$(PYTHON_LD) itex2MML_python.o y$(TYPE).tab_python.o lex$(TYPE).yy_python.o  $(LIBPYTHONARG) -o _itex2MML$(PYTHON_DLEXT)

test_python:
	$(PYTHON) ./ItexToMML.py

install_python:	python
	$(INSTALL) _itex2MML$(PYTHON_DLEXT) $(PYTHON_SITEDIR)
	$(INSTALL) itex2MML.py $(PYTHON_SITEDIR)
	$(INSTALL) itextomml.py $(PYTHON_SITEDIR)
