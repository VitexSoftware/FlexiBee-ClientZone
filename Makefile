all: fresh build install

fresh:
	echo fresh

install: 
	echo install
	
build:
	echo build

clean:
	rm -rf debian/clientzone 
	rm -rf debian/*.substvars debian/*.log debian/*.debhelper debian/files debian/debhelper-build-stamp

db:
	phinx migrate

deb:
	dpkg-buildpackage -A -us -uc

dimage:
	docker build -t vitexsoftware/clientzone .

dtest:
	docker-compose run --rm default install
        
drun: dimage
	docker run  -dit --name ClientZone -p 2323:80 vitexsoftware/clientzone
	nightly http://localhost:2323


.PHONY : install
	