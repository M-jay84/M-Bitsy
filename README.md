<p align="center">
<b>M-BiTsy</b><br>
<b>A BitTorrent System. A Pure PHP Tracker</b><br>
<b>MVC PDO OOP</b><br>
</p>

## <a name="introduction"></a> :page_facing_up: Introduction

<b>Welcome to My Bit-Torrent System - M-BiTsy </b><br>
A PHP torrent tracker working on the latest PHP 8. It uses PDO for the database and is written in a MVC structure.<br>

This is designed for a community tracker and includes a forum, shoutbox, staff panel, message system and so much more.<br>
Everything you would expect for a torrent tracker upload, import, announce, bencoding, udp & https scraping etc & also includes interaction with The Movie Database Api.<br>
Now supports v2 Torrents.<br>

There is support at https://torrenttrader.uk<br>

## <a name="features"></a> 💎 Some Features

  - Stack backtrace for exceptions
  - PDO Prepared Statements
  - v2 torrents
  - MVC Libs
  - Bootstrap 5
  - BCRYPT Passwords
  - Snatchlist
  - Hit & Run
  - Magnets
  - UDP Scraper
  - and MUCH MORE!

## <a name="requirements"></a> :white_check_mark: Requirements

- A Web server
- PHP 8
- MySQL 8 / MariaDB 10

## <a name="installation"></a> :computer: Installation

FOR XAMMP
1) Copy ALL folder to xampp.
2) Create A Database & Import SQL
3) Edit database & website address details in Core/paths.php.
3) Visit localhost & signup.

FOR OTHER
If you check public_html/.htaccess you will see its for xammp on line 4
/M-BiTsy/public_html
this needs to be changed depending on setup - public_html is normally where the index page is
On most servers index is root so replacing /M-BiTsy/public_html with / will work

If you root folder is called www or home for example public_html will need renaming
You will also have to change it in Mbitsy/.htaccass & /M-BiTsy/public_html/.htaccass