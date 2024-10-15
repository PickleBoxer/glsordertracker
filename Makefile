help:
	@egrep "^# target" Makefile

# target: help                            - Display this help message
# target: docker-build|db                 - Setup/Build PHP & (node)JS dependencies
db: docker-build
docker-build: build-back

# target: build-back                      - Install PHP dependencies using Composer
build-back:
	docker-compose run --rm php sh -c "composer install"

# target: build-back-prod                 - Install PHP dependencies for production
build-back-prod:
	docker-compose run --rm php sh -c "composer install --no-dev -o"

# target: build-zip                       - Create a zip file of the project excluding certain files
build-zip:
	cp -Ra $(PWD) /tmp/glsordertracker
	rm -rf /tmp/glsordertracker/.docker
	rm -rf /tmp/glsordertracker/.devcontainer
	rm -rf /tmp/glsordertracker/.env.test
	rm -rf /tmp/glsordertracker/.php_cs*
	rm -rf /tmp/glsordertracker/.travis.yml
	rm -rf /tmp/glsordertracker/cloudbuild.yaml
	rm -rf /tmp/glsordertracker/composer.*
	rm -rf /tmp/glsordertracker/package.json
	rm -rf /tmp/glsordertracker/.npmrc
	rm -rf /tmp/glsordertracker/package-lock.json
	rm -rf /tmp/glsordertracker/.gitignore
	rm -rf /tmp/glsordertracker/.editorconfig
	rm -rf /tmp/glsordertracker/.git
	rm -rf /tmp/glsordertracker/.github
	rm -rf /tmp/glsordertracker/tests
	rm -rf /tmp/glsordertracker/docker-compose.yml
	rm -rf /tmp/glsordertracker/Makefile
	mv -v /tmp/glsordertracker $(PWD)/glsordertracker
	zip -r glsordertracker.zip glsordertracker
	rm -rf $(PWD)/glsordertracker

# target: build-zip-prod                  - Launch prod zip generation of the module (will not work on windows)
build-zip-prod: build-back-prod build-zip

# target: php-cs-fixer                    - Run PHP CS Fixer in dry-run mode
php-cs-fixer:
	docker-compose run --rm php sh -c "php vendor/bin/php-cs-fixer fix --dry-run"
