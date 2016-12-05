=== WP Mail Smtp Mailer ===
Contributors: arshidkv12
Tags:  email, gmail, mail, mail smtp, outgoing mail, phpmailer, privacy, security, sendmail, smtp, ssl, tls, wordpress smtp, wp smtp, wp-phpmailer, wp_mail, wp mail smtp, WP Mail Smtp Mailer
Requires at least: 3.5
Tested up to: 4.6
Stable tag: 1.0.1
License: GPLv2

Reconfigures the wp_mail() function to use SMTP instead of mail() function and add password encryption
  

== Description ==

WP Mail Smtp Mailer allows you to configure and send all outgoing emails via a SMTP server. This will prevent your emails from going into the junk/spam folder of the recipients.

Go to `Settings > Mail Smtp Mailer` 

 
You can set the following options:
 
* Specify the from name and email address for outgoing email.
* Choose to send mail by SMTP or PHP's mail() function.
* Specify an SMTP host (defaults to localhost).
* Specify an SMTP port (defaults to 25).
* Choose SSL / TLS encryption (not the same as STARTTLS).
* Choose to use SMTP authentication or not (defaults to not).
* Specify an SMTP username and password.

Support : http://www.ciphercoin.com/contact/

= WP Mail Smtp Mailer Features = 
* Send email using a SMTP sever.
* You can use Gmail, Yahoo, Hotmail's SMTP server if you have an account with them.
* Securely deliver emails to your recipients.
* Username and password encryption 

> #### **List of SMTP Servers (Outgoing)**

> * **Gmail** :- Host: smtp.gmail.com - Secure(SSL) - Port(465)
> * **Gmail** :- Host: smtp.gmail.com - Secure(TLS) - Port(587)
> * **Outlook.com** :- Host: smtp-mail.outlook.com - Secure(TLS) - Port(587)
> * **Office365.com** :- Host: smtp.office365.com - Secure(TLS) - Port(587)
> * **Hotmail.com** :- Host: smtp.live.com - Secure(TLS) - Port(587)
> * **Yahoo Mail** :- Host: smtp.mail.yahoo.com  - Secure(TLS) - Port(587)
> * **Yahoo Mail** :- Host: smtp.mail.yahoo.com  - Secure(SSL) - Port(465)
> * **Yahoo Mail Deutschland** :- Host: smtp.mail.yahoo.com - Secure(SSL) - Port(465)
> * **Yahoo Mail Plus** :- Host: plus.smtp.mail.yahoo.com - Secure(SSL) - Port(465)
> * **AOL.com** :- Host: smtp.aol.com - Secure(TLS) - Port(587)
> * **AT&T** :- Host: smtp.att.yahoo.com - Secure(SSL) - Port(465)
> * **NTL @ntlworld.com** :- Host: smtp.ntlworld.com - Secure(SSL) - Port(465)
> * **BT Connect** :- Host: smtp.btconnect.com -  No-Encryption - Port(25)
> * **BT Openworld** :- Host: mail.btopenworld.com - No-Encryption - Port(25)
> * **BT Internet** :- Host: mail.btinternet.com - No-Encryption - Port(25)
> * **Orange** :- Host: smtp.orange.net - No-Encryption - Port(25)
> * **Orange UK** :- Host: smtp.orange.co.uk - No-Encryption - Port(25)
> * **Wanadoo UK** :- Host: smtp.wanadoo.co.uk - No-Encryption - Port(25)
> * **Comcast** :- Host: smtp.comcast.net - No-Encryption - Port(587)
> * **Yahoo Mail AU/NZ** :- Host: smtp.mail.yahoo.com.au - Secure(SSL) - Port(465)
> * **O2 Deutschland** :- Host: mail.o2online.de -  No-Encryption - Port(25)
> * **zoho Mail** :- Host: smtp.zoho.com - Secure(SSL) - Port(465)
> * **T-Online Deutschland** :- Host: securesmtp.t-online.de - Secure(TLS) - Port(587)
> * **1&1 (1and1)** :- Host: smtp.1and1.com - Secure(TLS) - Port(587)
> * **1&1 Deutschland** :- Host: smtp.1und1.de - Secure(TLS) - Port(587)
> * **Verizon** :- Host: outgoing.verizon.net - Secure (SSL) - Port(465)
> * **Verizon (Yahoo hosted)** :- Host: outgoing.yahoo.verizon.net - No-Encryption - Port(587)
> * **Mail.com** :- Host: smtp.mail.com - Secure(SSL) - Port(465)
> * **GMX.com** :- Host: smtp.gmx.com - Secure(SSL) - Port(465)
> * **Yahoo Mail UK** :- Host: smtp.mail.yahoo.co.uk - Secure(SSL) - Port(465)
> * **Airmail** :- Host: smtp.airmail.net - Secure(SSL) - Port(465)
> * **Bluewin.ch** :- Host: smtpauth.bluewin.ch - Secure(SSL) - Port(465)
> * **Eartlink.net** :- Host: smtpauth.earthlink.net - Secure(SSL) - Port(587)
> * **iCloud Mail** :- Host: smtp.mail.me.com - Secure(SSL) - Port(587)
> * **Rocketmail** :- Host: smtp.mail.yahoo.com - Secure(SSL) - Port(465)
> * **Rogers** :- Host: smtp.broadband.rogers.com - Secure(SSL) - Port(465)
> * **Ameritech.net** :- Host: smtp.mail.att.net - Secure(SSL) - Port(465)
> * **Pacbell** :- Host: smtp.mail.att.net - Secure(SSL) - Port(465)
> * **Swbell** :- Host: smtp.mail.att.net - Secure(SSL) - Port(465)
> * **Bellsouth** :- Host: smtp.mail.att.net - Secure(SSL) - Port(465)
> * **Flash** :- Host:- smtp.mail.att.net - Secure(SSL) - Port(465)

Note: These SMTP Ports and Settings may be different depending upon your Host Provider. Please contact your Web Server Host for correct details.



== Installation ==

1. Download and extract plugin files to a wp-content/plugin directory.
2. Activate the plugin through the WordPress admin interface.
3. Done !


== Screenshots ==
1. Admin 



 
