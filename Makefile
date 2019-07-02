SHELL := /bin/bash
APPNAME := inroads

SASS := $(shell command -v sassc 2> /dev/null)
MSGFMT := $(shell command -v msgfmt 2> /dev/null)
LANGUAGES := $(wildcard language/*/LC_MESSAGES)
JAVASCRIPT := $(shell find public -name '*.js' ! -name '*-*.js' ! -path '*vendor/*')

VERSION := $(shell cat VERSION | tr -d "[:space:]")
COMMIT := $(shell git rev-parse --short HEAD)

default: clean compile package

deps:
ifndef SASS
	$(error "sassc is not installed")
endif
ifndef MSGFMT
	$(error "msgfmt is not installed, please install gettext")
endif

clean:
	rm -Rf build/${APPNAME}
	rm -Rf public/css/.sass-cache

compile: deps $(LANGUAGES)
	cd public/css && sassc -mt compact screen.scss screen-${VERSION}.css
	for f in ${JAVASCRIPT}; do cp $$f $${f%.js}-${VERSION}.js; done

package:
	[[ -d build ]] || mkdir build
	rsync -rl --exclude-from=buildignore . build/$(APPNAME)
	cd build && tar czf $(APPNAME).tar.gz $(APPNAME)

$(LANGUAGES): deps
	cd $@ && msgfmt -cv *.po
