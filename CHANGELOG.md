# thumbhash - Changelog

## Version 1.0.0 - 14.05.2023

**Platzhalter für Bilder generieren mit ThumbHash.JS und ThumbHash.PHP**

**Hinweis:** Erste Version des AddOns. Es wird die PHP-Klasse `ThumbHash` und das JavaScript `ThumbHash` verwendet. Mehr Informationen gibt es hier [https://github.com/SRWieZ/thumbhash](https://github.com/SRWieZ/thumbhash) und dort [https://evanw.github.io/thumbhash/](https://evanw.github.io/thumbhash/)

### Features

* Bei Upload/Update wird der ThumbHash und das Vorschaubild automatisch im Medienpool gespeichert (Tabellen-Felder `thumbhash` und `thumbhashimg`)
* gültige Image-Typen: `JPEG, PNG, GIF, BMP, WBMP, WEBP`.
* Der ThumbHash kann über `rex_media` abgerufen werden, z.B. `rex_media::get('filename.jpg')->getValue('thumbhash');`
* Klasse `ForThumbHash` für den Zugriff auf die ThumbHash-Werte
* Console-Kommandos zum löschen und generieren der ThumbHash-Werte

### Bugfixes

 * noch Keine ;)
