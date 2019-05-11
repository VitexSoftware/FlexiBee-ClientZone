all: fresh build install

fresh:
	echo fresh

install: 
	echo install
	
build:
	echo build

clean:
	rm -rf debian/clientzone 
	rm -rf debian/*.substvars debian/*.log debian/*.debhelper debian/files debian/debhelper-build-stamp *.deb

db:
	phinx migrate

deb:
	dpkg-buildpackage -A -us -uc

dimage: deb
	mv ../clientzone_*_all.deb .
	docker build -t vitexsoftware/clientzone .

dtest:
	docker run -d -p 9002:80 --name clientzone  vitexsoftware/clientzone:latest
        
drun: dimage
	docker run  -dit --name ClientZone -p 9002:80 vitexsoftware/clientzone
	nightly http://localhost:9002/clientzone

dclean:
	docker rmi $(docker images |grep 'clientzone')

vagrant: clean
	vagrant destroy
	vagrant up

.PHONY : install
	
