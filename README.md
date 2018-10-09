# Forum

Source code for AppDev Ultras webforum

Installation:
We have used XAMPP to launch Apache and MySQL (phpmyadmin).

Note: Unless otherwise specified, when adding to "the config" (httpd.conf), add under "#Supplemental configuration".

1. First configure the Apache server by editing the config file (Default: C:\xampp\apache\conf\httpd.conf) (apache2.conf on Debian/ubuntu).
	1.1	Set file directory.
		Change the "DocumentRoot" and "Directory" (XAMPP-Apache line 251-252) paths to where our "html" directory is saved.  (E.g. "C:/xampp/htdocs/html")
		
	1.2 Disable directory browsing listing.
		In the same block of text (inside the <Directory> tags), (line 265) remove "Indexes" from the "Options" line.
	
	1.3	Under the "#Supplemental configuration" part in the config file (near the bottom, line 494), add "TraceEnable off", to disable tracing.
		(If you are going to add apache-plugins that include TRACK, this should be sufficient, as newer Apache versions do not have TRACK natively.
		 In the case that you are running an old version/using plugins with TRACK, add:
		 
			RewriteEngine On
			RewriteCond %{REQUEST_METHOD} ^(TRACE|TRACK)
			RewriteRule .* - [F]
		  
		 instead. This disables both TRACK and TRACE.
		 You can test for this by running "curl -k -X TRACE http://localhost" and "curl -k -X TRACK http://localhost" in Linux. (substitute the ip for whatever you are using).
		 If output says something along the lines of "Not allowed", it is successfully disabled.

	1.4	Remove server version banner by adding:
		
			ServerTokens Prod
			ServerSignature Off
		
		in the config. 
		This might be overruled by extra/httpd-default.conf (At least for XAMPP). Find the same lines in that file, and change them to "Prod" and "Off" as well.

	1.5 Disable ETag.
		Add:
		
			FileETag None
			
		to the config.
		
	1.6 Prevent Clickjacking
		Open the config file, ensure the module "mod.headers.so" is enabled (~Line 120 is NOT commented out).
		Add:
		
			Header always append X-Frame-Options SAMEORIGIN
			
		to the config.
		
	1.7 Add X-XSS Protection.
		Add:
		
			Header set X-XSS-Protection "1; mode=block"
		
		to the config.
				
	
2. Add the database (and testdata) to MySQL.
	2.1	Copy the contents of "databaseInit.txt", or import the database file into MySQL.
	
	