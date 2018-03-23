test: test-unit
lint: lint-md lint-cs

# Run every available unit test
test-unit:
	vendor/bin/phpunit --bootstrap vendor/autoload.php test

# Run PHP Mess Detector against all the code, settings are in phpmd.xml
lint-md:
	vendor/bin/phpmd TplBlock.php text ./phpmd.xml
	vendor/bin/phpmd test/TplBlockTest.php text ./phpmd.xml
	vendor/bin/phpmd sample/sample.php text ./phpmd.xml

# Run PHP Code Sniffer against all the code, settings are in phpcs.xml
lint-cs:
	vendor/bin/phpcs TplBlock.php -s
	vendor/bin/phpcs test/TplBlockTest.php -s
	vendor/bin/phpcs sample/sample.php -s
