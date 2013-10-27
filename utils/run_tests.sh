#! /bin/bash

ROOT=$1;
[ $1 == '' ] && ROOT=".";

ENV=$2;
[ $2 == '' ] && ENV="dev";

echo "Repo root = $ROOT";
cd $ROOT;
##### PHP lint
echo "PHP Lint check";
for mod in $(git status  --porcelain | egrep "^[^Dd].*\.php$" | awk '{ print $2 }')
do
	/usr/bin/php -l $mod;
	[ $? -ne 0 ] && exit 1;
	## echo "PHP Lint check on $mod completed and passed";
done

##### Unit test
echo "Executing PHPUnit test suite";
vendor/bin/phpunit -c config/test.xml
[ $? -ne 0 ] && exit 1;

##### behat test
echo "Executing Behat feature test suuite";
bin/behat features ${ENV};
[ $? -ne 0 ] && exit 1;

exit 0;
