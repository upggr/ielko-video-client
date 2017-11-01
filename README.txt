IELKO WP MEDIA MANAGER

Purpose of this plugin :

Manage remote or local media from a wordpress installation and provide xml feeds. Also currently a roku and tvos app is generated using a third party open source project automatically from the same interface.

Installation instructions :

Just copy the plugin in the plugins folder and activate it from the wordpress menu (or download this repo as a zip and upload it using the wp interface). There is another plugin dependency not included with the plugin "category images". You will receive notice if you do not have it and be prompted to install it from the wordpress menu.

Usage :
 
Assuming your wordpress installation is at : http://mywordpress.com/

STEP 1 : Add media sources using the Ielko Media menu in the menu, as if you were creating a new wordpress post. Complete the title of the media (for example "my awesome video"), and at least the source (for example http://mydomain.com/my_awesome_video.mp4). You will need to define a category for each item you create, and you can upload an image for the categories using the interface. You will need to have a top category (for example Live Video) and then under this the subcategories. for the direct publisher it doesnt matter but for the others it does!.

STEP 2 : Complete the fields bellow on this page.

STEP 3 : Download your ROKU app by clicking here (Watch out for your popup blocker).

STEP 4 : Download your TVOS app by clicking here (Watch out for your popup blocker).

STEP 5 : Feeds :

Your ROKU feed is accessible from http://your_domain.com/?feed=roku
Your categoryleaf based ROKU feed is accessible from http://your_domain.com/?feed=roku_cats
Your direct publisher ROKU feed is accessible from http://your_domain.com/?feed=roku_dp
Your TVOS feed is accessible from http://your_domain.com/?feed=tvos
Your Android (Variant 1) feed is accessible from http://your_domain.com/?feed=android1
The dead link checker is at : http://your_domain.com/?feed=checkdead
The remote updater is at : http://your_domain.com/?feed=remoteupdate&remotefeed=http://THEREMOTEFEEDTOCOMPARE


Notes :

Note that this is still in beta mode. The roku app should work ok provided you complete the fields bellow. the tvos app, will compile fine on your mac and will run fine on the simulator or your tvos device as soon as you press play, but I have not created the logic to incorporate the media assets yet, so you will just see some images (splash screens, icons) from an existing app I am using.

To do :

Add more players for lg, samsung, android, ios

Add more feed types for compatibility with kodi, plex channels, vlc etc

Create a matching template so that there is a full website using the links in the same wordpress installation

Download the plugin : here


here are some feeds that are compatible with the checker functions (/?feed=remoteupdate&remotefeed=):

http://alivegr.net/raw/alivegr.
https://raw.githubusercontent.com/free-greek-iptv/greek-iptv/master/android.m3u