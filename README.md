## Installation
```bash
git clone https://github.com/cirolaferrara/forum-downloader.git
cd forum-downloader/
composer update
```
## Usage
```bash
php index.php -q 192.168.1.246 -e mladmin -r mlpassword -t forumuser -y forumpass -u "http://mylink.com/viewtopic.php?f=79&t=145416"
```
## Params
```
-e/--mldonkey-username <argument>
     Required. Mldonkey username

-u/--forum-page <argument>
     Required. Forum page

-y/--forum-password <argument>
     Required. Forum password

-t/--forum-username <argument>
     Required. Forum username

--help
     Show the help page for this command.

-q/--mldonkey-host <argument>
     Required. Mldonkey host

-r/--mldonkey-password <argument>
     Required. Mldonkey password


-w/--mldonkey-port <argument>
     Mldonkey port
``