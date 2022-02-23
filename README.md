[![WordPress tested 5.5](https://img.shields.io/badge/WordPress-v5.5%20tested-0073aa.svg)](https://wordpress.org/plugins/bh-wp-autologin-urls) [![PHPCS WPCS](https://img.shields.io/badge/PHPCS-WordPress%20Coding%20Standards-8892BF.svg)](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards) [![PHPUnit ](.github/coverage.svg)](https://brianhenryie.github.io/bh-wp-github-actions-tests/)

# IMAP One Click List Unsubscribe

https://tools.ietf.org/html/rfc2369

https://tools.ietf.org/html/rfc8058



### RFC2369: The Use of URLs as Meta-Syntax for Core Mail List Commands and their Transport through Message Header Fields

The three core header fields described in this document are List-Help, List-Subscribe, and List-Unsubscribe.
... and ...    List-Post, List-Owner and List-Archive.

List-Help
field is the most useful individual field since it provides an access
point to detailed user support information, and accommodates almost
all existing list managers command sets. 


A list of multiple, alternate, URLs MAY be specified by a comma-
separated list of angle-bracket enclosed URLs. The URLs have order of
preference from left to right. 

3.2. List-Unsubscribe

The List-Unsubscribe field describes the command (preferably using
mail) to directly unsubscribe the user (removing them from the list).

Examples:

List-Unsubscribe: <mailto:list@host.com?subject=unsubscribe>
List-Unsubscribe: (Use this command to get off the list)
    <mailto:list-manager@host.com?body=unsubscribe%20list>
List-Unsubscribe: <mailto:list-off@host.com>


List-Unsubscribe: <http://www.host.com/list.cgi?cmd=unsub&lst=list>,
 <mailto:list-request@host.com?subject=unsubscribe>

Message headers are an existing standard, designed to
easily accommodate new types.

#### rfc8058

A mail sender that wishes to enable one-click unsubscriptions places
   one List-Unsubscribe header field and one List-Unsubscribe-Post
   header field in the message.  The List-Unsubscribe header field MUST
   contain one HTTPS URI.  It MAY contain other non-HTTP/S URIs such as
   MAILTO:.  The List-Unsubscribe-Post header MUST contain the single
   key/value pair "List-Unsubscribe=One-Click".  As described below, the
   message MUST have a valid DomainKeys Identified Mail (DKIM) signature
   that covers at least the List-Unsubscribe and List-Unsubscribe-Post
   headers.


(relevant for login)
   The mail sender MUST NOT return an HTTPS redirect, since redirected
   POST actions have historically not worked reliably, and many browsers
   have turned redirected HTTP POSTs into GETs.


The content
   of the List-Unsubscribe-Post header is limited to a single known key/
   value pair to prevent an attacker from creating malicious messages
   where the POST operation could simulate a user filling in an
   arbitrary form on a victim website.
#















Adds an email address to the One-Click List-Unsubscribe Header and checks that inbox for unsubscribe emails.

Edit the one click list unsubscribe header to contain a return email address.  


Unsubscribe them.

Need: 
* imap mailbox details: server, port, username, password
* regex to match subject


https://tools.ietf.org/html/rfc8058



What about when a server has both plugins installed? Should have an indicator in the subject which integration its for



$GLOBALS['bh_wp_imap_one_click_list_unsubscribe']->api->check_for_unsubscribe_emails();


brew tap kabel/php-ext
brew install php@7.4-imap 
php -r 'include "vendor/ssilence/php-imap-client/autoload.php";'

brew update && brew upgrade
brew install libffi




If using AWS Bounce Handler, the return emails can be routed to a subdomain using AWS SES to an SNS notification.



```
  Problem 1
    - Installation request for ssilence/php-imap-client dev-master -> satisfiable by ssilence/php-imap-client[dev-master].
    - ssilence/php-imap-client dev-master requires ext-imap * -> the requested PHP extension imap is missing from your system.

  To enable extensions, verify that they are enabled in your .ini files:
    - /usr/local/etc/php/7.4/php.ini
    - /usr/local/etc/php/7.4/conf.d/ext-opcache.ini
    - /usr/local/etc/php/7.4/conf.d/ext-xdebug.ini
  You can also run `php --ini` inside terminal to see which files are used by PHP in CLI mode.
```

php -r "file_put_contents( 'auth.json', json_encode( [ 'http-basic' => [ 'blog.brianhenry.ie' => [ 'username' => '"${{ secrets.COMPOSER_AUTH_SECRET }}"', 'password' => 'satispress' ] ] ] ) );"





## Contributing

Clone this repo, open PhpStorm, then run `composer install` to install the dependencies.

```
git clone https://github.com/brianhenryie/plugin_slug.git;
open -a PhpStorm ./;
composer install;
```

For integration and acceptance tests, a local webserver must be running with `localhost/plugin_slug/` pointing at the root of the repo. MySQL must also be running locally – with two databases set up with:

```
mysql_username="root"
mysql_password="secret"

# export PATH=${PATH}:/usr/local/mysql/bin

# Make .env available to bash.
export $(grep -v '^#' .env.testing | xargs)

# Create the databases.
mysql -u $mysql_username -p$mysql_password -e "CREATE USER '"$TEST_DB_USER"'@'%' IDENTIFIED WITH mysql_native_password BY '"$TEST_DB_PASSWORD"';";
mysql -u $mysql_username -p$mysql_password -e "CREATE DATABASE "$TEST_SITE_DB_NAME"; USE "$TEST_SITE_DB_NAME"; GRANT ALL PRIVILEGES ON "$TEST_SITE_DB_NAME".* TO '"$TEST_DB_USER"'@'%';";
mysql -u $mysql_username -p$mysql_password -e "CREATE DATABASE "$TEST_DB_NAME"; USE "$TEST_DB_NAME"; GRANT ALL PRIVILEGES ON "$TEST_DB_NAME".* TO '"$TEST_DB_USER"'@'%';";
```

### WordPress Coding Standards

See documentation on [WordPress.org](https://make.wordpress.org/core/handbook/best-practices/coding-standards/) and [GitHub.com](https://github.com/WordPress/WordPress-Coding-Standards).

Correct errors where possible and list the remaining with:

```
vendor/bin/phpcbf; vendor/bin/phpcs
```

### Tests

Tests use the [Codeception](https://codeception.com/) add-on [WP-Browser](https://github.com/lucatume/wp-browser) and include vanilla PHPUnit tests with [WP_Mock](https://github.com/10up/wp_mock). 

Run tests with:

```
vendor/bin/codecept run unit;
vendor/bin/codecept run wpunit;
vendor/bin/codecept run integration;
vendor/bin/codecept run acceptance;
```

Output and merge code coverage with:

```
vendor/bin/codecept run unit --coverage unit.cov; vendor/bin/codecept run wpunit --coverage wpunit.cov; vendor/bin/phpcov merge --clover tests/_output/clover.xml --html tests/_output/html tests/_output --text;
```

To save changes made to the acceptance database:

```
export $(grep -v '^#' .env.testing | xargs)
mysqldump -u $TEST_SITE_DB_USER -p$TEST_SITE_DB_PASSWORD $TEST_SITE_DB_NAME > tests/_data/dump.sql
```

To clear Codeception cache after moving/removing test files:

```
vendor/bin/codecept clean
```

### More Information

See [github.com/BrianHenryIE/WordPress-Plugin-Boilerplate](https://github.com/BrianHenryIE/WordPress-Plugin-Boilerplate) for initial setup rationale. 

# Acknowledgements
