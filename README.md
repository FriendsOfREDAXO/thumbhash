# ThumbHash - Platzhalter für Bilder generieren

# mit ThumbHash.JS und ThumbHash.PHP

![logo](https://github.com/FriendsOfREDAXO/thumbhash/blob/assets/thumbhash.png?raw=true)

<p align="center">
    <a href="https://github.com/FriendsOfREDAXO/thumbhash/releases"><img src="https://img.shields.io/github/release/FriendsOfREDAXO/thumbhash.svg?style=for-the-badge" alt=""></a>&nbsp;
    <a href="https://github.com/FriendsOfREDAXO/thumbhash/blob/master/LICENSE"><img src="https://img.shields.io/badge/license-MIT-green.svg?longCache=true&style=for-the-badge" alt=""></a>&nbsp;
</p>

## Überblick

Das AddOn `thumbhash` erweitert den REDAXO Medienpool um _automatisch_ generierte Platzhalter für Bilder.
Beim Upload oder Änderung von Bildern im Medienpool werden automatisch die Daten für die Platzhalter berechnet und im Medienpool gespeichert.
Es werden die beiden Extension-Points `MEDIA_ADDED` und `MEDIA_UPDATED` verwendet.

**"Einfach und grob"** erklärt werden die Bilddateien auf eine maximale Kantenlänge von 100px verkleinert. Auf dieses verkleinerte Bild wird dann der Algorithmus von Evan Wallace [(@evanw)](https://github.com/evanw) mit Hilfe der PHP-Klasse von Eser DENIZ [(@SRWieZ)](https://github.com/SRWieZ) angewendet.

**Ergebnis** ist ein weichgezeichnetes PNG-Bild mit einer maximalen Kantenlänge von 32px und ein String - mit ca. maximal 34 Bytes - der in dieses PNG-Bild umgerechnet werden kann.

Diese beiden Werte werden in der Medienpool-Tabelle `rex_media` in den Spalten `thumbhash` und `thumbhashimg` gespeichert und können im Anschluss für die Ausgabe auf der Website genutzt werden.

Das AddOn `thumbhash` liefert PHP und JavaScript-Methoden für die Erstellung und Verwendung der ThumbHash-Daten.

> **Hinweis:** Wer mehr darüber erfahren möchte kann gerne unter folgendem Link https://evanw.github.io/thumbhash/ nachlesen und versuchen zu verstehen ;-)
Auf der Website gibt es auch Beispiele und man kann mit eigenen Bildern testen.

## MedienPool

Der Medienpool (Tabelle `rex_media`) wird durch die Installation des AddOns um die zwei Felder `thumbhash` und `thumbhashimg` erweitert.

### Gültige Bilddateien für ThumbHashes

Für folgende Bildformate können _ThumbHashes_ erstellt werden:

```
'jpeg', 'jpg', 'png', 'gif', 'bmp', 'wbmp', 'webp'
```

** Beispiel thumbhash**

```
n/cJJYZRiHdReHmwd1h6VnVwXAfH
```

** Beispiel thumbhashimg**

```
data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAXCAYAAABqBU3hAAAMEElEQVR4AQCBAH7/AAALLP8ADCz/AA4u/wAQL/8AEjH/ ...
```

Im folgenden Screenshot werden die beiden Felder unter dem Vorschaubild angezeigt. Hierfür wird der Extension-Point `MEDIA_DETAIL_SIDEBAR` verwendet. Sollten noch keine ThumbHash-Werte angezeigt werden einfach auf **aktualisieren** klicken, dann werden die Werte berechnet, im Medienpool gespeichert und auch in der Detailansicht angezeigt.

![logo](https://github.com/FriendsOfREDAXO/thumbhash/blob/assets/mediapool.png?raw=true)

Der angezeigte **ThumbHash** `n/cJJYZRiHdReHmwd1h6VnVwXAfH` kann per PHP im Backend oder per JS im Frontend in das Vorschaubild _umgerechnet_ werden.

Mit der Klase `rex_media` kann einfach auf die beiden Felder zugegriffen werden.

### Zugriff auf die ThumbHash-Daten

```php
$media = rex_media::get('thumbhash.jpg');
if (null !== $media) {
    $thumbhash = $media->getValue('thumbhash');
    $thumbhashimg = $media->getValue('thumbhashimg');
}
```

> **Hinweis:** Empfohlen wird die Verwendung der Klasse `ForThumbHash` statt `rex_media`. Warum? Weiterlesen!

## ThumbHash Verwenden

Warum sollte ich die Klasse `ForThumbHash` verwenden und nicht die Daten direkt aus dem Medienpool (`rex_media`)?
(Natürlich können auch die Daten direkt aus dem Medienpool verwendet werden, hängt vom Workflow/Projekt ab)

**Ganz einfach**

Bei Verwendung der Klasse `ForThumbHash` wird, falls die ThumbHash-Daten noch nicht im Medienpool gespeichert sind, diese neu berechnet und automatisch im Medienpool gespeichert. Die berechneten Daten werden auch gleich zurückgeliefert. Sind die ThumbHash-Daten bereits vorhanden findet keine neue Berechnung statt. Bei Verwendung der Daten über die Klasse `rex_media` gibt es bei noch nicht vorhandenen ThumbHash-Daten _Broken-Images_.

### Verwendung des ThumbHashes mit den Attributen `data-thumbhash` und `data-thumbhashsrc`

Die Methode `getThumbHash` liefert den ThumbHash-Wert aus dem Medienpool oder neu berechnet aus der Medienpool-Datei.

```php
<img
    data-thumbhash="<?= \FriendsOfRedaxo\ThumbHash\ForThumbHash::getThumbHash('thumbhash.jpg'); ?>"
    data-thumbhashsrc="media/thumbhash.jpg" width="200" height="132" loading="lazy"
/>
```

wird zu ...

```php
<img
    data-thumbhash="n/cJJYZRiHdReHmwd1h6VnVwXAfH"
    data-thumbhashsrc="media/thumbhash.jpg" width="200" height="132" loading="lazy"
/>
```

### Verwendung des ThumbHash-Images mit dem Attribut `data-thumbhashimg`  und `data-thumbhashsrc`

Die Methode `getThumbHashImg` liefert das ThumbHash-Image aus dem MedienPool oder neu berechnet aus der MedienPool-Datei.

```php
<img
    data-thumbhashimg="<?= \FriendsOfRedaxo\ThumbHash\ForThumbHash::getThumbHashImg('thumbhash.jpg'); ?>"
    data-thumbhashsrc="media/thumbhash.jpg" width="200" height="132" loading="lazy"
/>
```

wird zu ...

```php
<img
    data-thumbhashimg="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAXCAYAAABqBU3hAAAMEElEQVR4AQCBAH7/AAALLP8ADCz/AA4u/wAQL/8AEjH/ABMx/wAVMv8AFjL/ABgz/wQbNf8KIDj/EiY+/xsuRf8lN0z/Lz9T/zZFWf87SVv/PUpb/zxIWP84Q1P/Mz1M/y43Rf8qMkD/Jy89/ycuO/8pMD3/LTNA/zI4RP83PUn/O0JO/z9FUf9BR1P/AIEAfv8AABM0/wAUNP8AFjb/ABg3/wAZOP8AGzn/ABw5/wMdOv8GHzr/CiI8/xAmQP8YLUX/ITRM/ys9U/80RVr/PEtf/0FPYv9CUGL/QU5f/z5JWf85Q1P/ND5N/zA5R/8tNkT/LTVD/y83RP8zOkf/Nz9M/zxEUP9BSFX/REtY/0ZNWv8AgQB+/wAAIEL/ASFD/wMjRP8FJUb/CCZH/wooR/8MKUf/DipH/xErSP8VLkn/GjJN/yI4Uv8rQFj/NEhf/z1QZv9FVmv/Slpu/0xbbv9LWWv/SFVm/0NPYP8/Slr/O0ZV/zlDUv84QlH/OkRS/z5HVf9CS1r/R1Be/0xUYv9PV2X/UFln/wCBAH7/AA4wVP8PMVX/ETNW/xQ1V/8WN1n/GDhZ/xo5Wf8cOVn/HjtZ/yI9Wv8nQV3/Lkdi/zdOaP9AVm//SV51/1Fke/9WaH7/WGl+/1hofP9VZHf/UWBy/01bbf9KV2j/SFRl/0hUZf9KVmb/Tllp/1Jdbf9WYXH/WmV1/11oeP9faXn/AIEAfv8AHEBm/x1BZv8fQ2j/IkVp/yRHa/8mSGv/KElr/ypKa/8sS2v/ME1s/zVQbv87VnP/RF15/01lf/9WbIb/XnOL/2N3j/9meZD/ZnmO/2V2i/9icof/Xm6C/1xrfv9aaXz/W2l8/11rff9gboD/ZHKD/2h1h/9seYv/b3yN/3B9j/8AgQB+/wAnTXT/KE51/ypQd/8tUnj/MFR6/zJWev80V3v/Nld6/zhYev87W3v/QF5+/0dkgv9Paoj/WHKP/2F6lv9qgZv/cIaf/3OJof91iaH/dIif/3KFm/9wgpj/boCV/25/lP9vf5T/cYGV/3SEmP94h5v/e4uf/3+Oov+BkKT/gpKl/wCBAH7/AC1Wfv8vV3//MVmB/zRbg/83XYT/Ol+F/zxghv8+YYb/QGOG/0Rlh/9JaIr/T26O/1h1lP9hfZv/a4Wi/3ONqf96k67/f5aw/4GYsf+CmLD/gpau/4GVrP+AlKv/gJOq/4KUq/+Elq3/h5mv/4udsv+OoLX/kaK4/5Okuv+Upbr/AIEAfv8AL1mC/zFag/80XIX/N1+H/zpiiv89ZIv/QGaM/0JnjP9FaI3/SWuO/05vkf9VdZb/Xnyc/2eEo/9xjav/epWy/4KcuP+Iobz/jKO+/46lvv+Ppb7/j6S9/5Ckvf+Rpb3/k6e+/5WpwP+YrMP/m6/G/56xyP+gs8r/orXM/6O2zP8AgQB+/wAvWIL/MVqD/zNchf83YIj/O2OK/z5ljP9BZ47/RGmP/0drkP9LbpH/UXKV/1h4mf9hgKD/a4io/3WSsP9/mrj/iKK+/46nw/+Tq8b/l67I/5mvyf+asMn/nLHK/56zy/+gtc3/o7fP/6a60f+ovNT/qr7W/6zA1/+twdj/rsHZ/wCBAH7/AC5Xf/8wWID/M1uD/zdehv87Yon/P2WL/0Jnjf9Gao7/SWyQ/05vkv9TdJX/W3qa/2SBof9uiqn/eJSx/4Kduf+LpcD/k6vG/5iwyv+ds83/oLbP/6K40P+kudH/p7vT/6m+1f+swNf/rsLZ/7HE2/+yxdz/s8bd/7TH3v+0x97/AIEAfv8AMFZ8/zFXfv81WoD/OV6E/z1ih/9CZYr/RWiM/0lqjf9MbY//UXCR/1d1lf9ee5r/Z4Kg/3GLqP97lLD/hZ24/46lv/+WrMX/nLHK/6C1zf+kuM//p7rR/6q90/+sv9X/r8HX/7HD2f+zxNr/tMbb/7XG3P+1xtz/tcbc/7XG3P8AgQB+/wA1WHv/N1l9/zpcf/8/YIP/Q2SG/0hoif9Ma4v/T22N/1Nwjv9Xc5H/XHeU/2N9mP9shJ//dYym/3+Vrv+InbX/kaW8/5irwv+esMb/o7TJ/6a3zP+puc7/rLvP/6690f+wv9L/ssDT/7PB1P+zwdT/s8HU/7LB0/+ywNP/scDS/wCBAH7/AEBdfP9CX37/RWKA/0lmhP9Oaof/Um2K/1ZwjP9Zco7/XXWP/2F3kf9le5T/bICY/3OGnf98jqP/hJWq/42dsf+VpLf/m6q8/6GuwP+lscL/qLTE/6q1xv+st8f/rrjI/6+5yf+vusn/r7nI/6+5yP+ut8b/rLbF/6u1xP+qtMP/AIEAfv8ATmV//1Bngf9UaoT/WG6H/1xyiv9gdY3/ZHiP/2d6kP9qe5H/bX6S/3GBlP92hZf/fYqc/4SQof+Ml6f/k52s/5qjsf+fp7X/o6u3/6atuf+orrr/qq+7/6uwu/+ssbv/rLG7/6ywuv+rr7n/qa23/6ertf+lqLL/o6ex/6KmsP8AgQB+/wBfb4P/YXGE/2Rzh/9od4r/bHqN/3B+j/9zgJH/doKS/3iDkv97hJP/foeU/4OKlv+Ijpr/jpOe/5SYov+anab/oKGq/6SlrP+mp67/qKiu/6morv+qqa7/qqiu/6qorf+pp6z/qKWq/6ajqP+joKX/oJ2i/52an/+bmJz/mZab/wCBAH7/AG93hP9xeYb/dHuI/3h/i/98go7/gIWQ/4KHkf+FiJL/h4mS/4mKkv+LjJP/j46U/5ORlv+YlZn/nZmd/6KdoP+moKL/qKKj/6qjo/+ro6P/q6Ki/6uiof+qoaD/qZ+e/6eenP+lm5r/opiW/5+Vk/+bkY//mI2L/5WKiP+TiYf/AIEAfv8AfHyC/359g/+BgIb/hYOJ/4iGi/+MiY3/jouO/5CMj/+SjI7/lI2O/5aOjv+ZkI//nJKR/6GWk/+lmZX/qJuX/6uemf+tn5n/rp+Z/66el/+tnZb/rJuU/6uakv+pmJD/p5aO/6WTi/+hj4f/nYuD/5mHf/+Vg3v/koB3/5B+dv8AgQB+/wCEe3v/hn18/4h/fv+MgoH/kIaE/5OIhv+Wiof/mIuH/5mMh/+bjIf/nY2H/6CPh/+jkYn/p5SL/6qWjP+umY7/sJqP/7Gbj/+ymo7/sZmM/7CYiv+vloj/rZSG/6yShP+qkIH/p41+/6OJe/+fhXb/m4Fy/5d9bv+TeWr/knhp/wCBAH7/AIZ2b/+Id3D/inpy/459df+SgHj/lYN6/5iFe/+ahnz/nId8/56IfP+giXz/o4t9/6aNfv+qkID/rpKC/7GVhP+zloT/tJaE/7WWg/+0lYL/s5SA/7KSfv+xkXz/sI96/66NeP+rinX/qIdy/6SCbf+ffmn/m3pl/5h3Yv+WdWD/AIEAfv8Ag2xg/4VuYf+IcGP/jHNm/493af+Temv/lnxt/5l+bv+bf27/nYBv/6CCb/+jhHH/p4dz/6uKdf+vjXf/s5B5/7WRev+3knr/t5J6/7eReP+3kHf/to92/7WOdf+0jXP/s4tx/7GJb/+uhmz/qoJo/6Z+ZP+iemD/n3dd/511W/8AgQB+/wB+YVD/f2NS/4JlVP+GaVf/imxa/45wXf+Scl//lXRg/5d2Yf+aeGL/nXpj/6F9Zf+mgGj/qoRr/6+Hbf+zinD/to1x/7iOcv+5jnL/uY5x/7mOcP+5jXD/uY1v/7mMbv+4i23/topr/7SHaf+xhGX/rYBh/6l8Xv+meVv/pXhZ/wCBAH7/AHhYRP96WUX/fVxI/4FgS/+FY07/iWdR/41qU/+QbFX/k25W/5dxWP+bc1r/n3dc/6R7X/+pf2L/roNm/7KGaP+2iWr/uIts/7qMbP+7jGz/u4xs/7yMa/+8jWv/vY1r/7yMa/+7i2n/uYln/7aGZP+zgmH/r39d/618Wv+re1n/AYEAfv8AdFI9/3ZUPv95V0H/fVpE/4JeR/+GYkr/imVN/45oT/+RalD/lW1S/5lwVP+dc1f/o3da/6h8Xv+tgGH/soRl/7aHZ/+4iWj/uopp/7uLaf+8i2n/vYxp/76Mav+/jWr/v41q/76Maf+8imf/uYdk/7aEYf+zgF3/sH5b/698Wf+8dxQJDpdswAAAAABJRU5ErkJggg=="
    data-thumbhashsrc="media/thumbhash.jpg" width="200" height="132" loading="lazy"
/>
```

Jetzt wird im Frontend aber noch kein Bild angezeigt. Für die Anzeige der Bilder muss noch ein `JavaScript` im Frontend eingebunden werden das ...

- die Umrechnung des Hash-Wertes in das Vorschau-Bild übernimmt
- das Vorschau-Bild dem `<img>`-Tag zuweist
- das Original-Bild lädt, und nach dem Laden das Vorschaubild durch das Original-Bild ersetzt

> **Hinweis:** Bei den Bildern unbedingt `width`, `height` und `loading="lazy"` angeben!

> **Hinweis:** Für das Frontend das notwendige JavaScript einbinden!

Natürlich kann das ThumbHash-Image auch direkt für das src-Attribut verwendet werden ;)
z.B. wenn bereits eigene LazyLoad-Scripte auf der Website im Einsatz sind.

```php
<img
    src="<?= \FriendsOfRedaxo\ThumbHash\ForThumbHash::getThumbHashImg('thumbhash.jpg'); ?>"
    data-src="media/thumbhash.jpg" width="200" height="132" loading="lazy"
/>
```

### **JavaScript im Frontend einbinden**

Bei Verwendung der ThumbHashes muss im Frontend zusätzlich noch ein JavaScript eingebunden werden das

- die Umrechnung des Hash-Wertes in das Vorschau-Bild übernimmt
- das as Vorschau-Bild dem `<img>`-Tag zuweist
- das Original-Bild lädt, und nach dem Laden das Vorschaubild durch das Original-Bild ersetzt

Ok, das hatten wir oben schon. Hier ein Beispiel wie der HTML-Code der Website ungfähr aussehen sollte ...

```php
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8" />
    <title></title>
</head>
<body>

    <div class="content">
        <img data-thumbhashsrc="media/thumb.jpg" data-thumbhash="NvgJDYJ4mWh/iHeSZ7Z2hXpgjir3" width="200" height="132" />
        <img data-thumbhashsrc="media/thumb2.jpg" data-thumbhash="EwgODIYHiHaLZ5d0h4YngIMIVw" width="200" height="112" />
        <img data-thumbhashsrc="media/thumb3.jpg" data-thumbhash="p/cJDYKguqmaZXaraWeEnP7G9Wxa" width="200" height="133" />
        <img data-thumbhashsrc="media/thumb4.jpg" data-thumbhash="kggGHYQJqaWIh3p1iadohQl6lKBH" width="200" height="132" />
        <img data-thumbhashsrc="media/thumb5.jpg" data-thumbhash="6wcKBwBQm3irZ3a7Z1eFenh46EIGH2YA" width="200" height="200" />
        <img data-thumbhashsrc="media/thumb6.jpg" data-thumbhash="MykGDQLzWodqx0lUmGKs2H+B9hZX" width="132" height="200" />
    </div>

    <footer>
    </footer>

<!-- ThumbHash-Script vor den anderen Scripten einbinden! -->
<?= \FriendsOfRedaxo\ThumbHash\ForThumbHash::getScriptTag() . PHP_EOL; ?>

<!-- oder als Inline-Script ... -->
<?= \FriendsOfRedaxo\ThumbHash\ForThumbHash::getScript() . PHP_EOL; ?>

<!--
... hier andere JavaScripte
-->

</body>
</html>
```

Es gibt je nach Vorlieben zwei Möglichkeigen um das notwendige JavaScript einzubinden.

1. Als Script-Tag

```php
<?= \FriendsOfRedaxo\ThumbHash\ForThumbHash::getScriptTag() . PHP_EOL; ?>
```

2. oder als Inline-Script

```php
<?= \FriendsOfRedaxo\ThumbHash\ForThumbHash::getScript() . PHP_EOL; ?>
```

Das standardmäßig mitgelieferte JavaScript (`thumbhash_fe.min.js`) setzt bei Images mit dem Attribut `data-thumbhash` das Vorschaubild aus dem berechneten ThumbHash.
Bei vorhandenem Attribut `data-thumbhashsrc` wird automatisch auch das originale Bild gesetzt.

Bei Bildern mit dm Attribut `data-thumbhashimg` wird das Vorschaubild direkt mit dem gelieferten Inline-PNG gesetzt.
Haben die Bilder das Attibut `data-thumbhashsrc` wird auch automatisch das originale Bild gesetzt und geladen.

Bei Verwendung von LazyLoad-Scripten kann evtl. auch das Attribut `data-src` statt `data-thumbhashsrc` verwendet werden.

> **Hinweis**: Das Script _vor_ `</body>` und _vor_ anderen JavaScripten einbinden!

## ThumbHashes löschen und generieren

Die gespeicherten ThumbHashes können mit _PHP_ oder per _Console-Kommando_ gelöscht werden.

**Löschen mit PHP**

```php
<?php
\FriendsOfRedaxo\ThumbHash\ForThumbHash::clearThumbHashes();
```

**Löschen mit REDAXO-Console-Kommando**

```console
php redaxo/bin/console thumbhash:clear [-y]
```

**Alle ThumbHash-Daten über das AddOn `adminer` löschen**

```sql
UPDATE `rex_media` SET `thumbhash` = '', `thumbhashimg` = '';
```

Die ThumbHashes für Bilder können mit _PHP_ oder per _Console-Kommando_ generiert werden.

**Generieren mit PHP**

```php
$count = \FriendsOfRedaxo\ThumbHash\ForThumbHash::createThumbHashes();
```

**Generieren mit REDAXO-Console-Command**

```console
php redaxo/bin/console thumbhash:create [-y]
```

## Klasse ForThumbHash

Die Klasse `ForThumbHash` stellt alle notwendigen Methoden für die Verwendung bzw. Erstellung der ThumbHashes bereit.

Aufruf in Modulen usw. z.B. wie folgt

```php
\FriendsOfRedaxo\ThumbHash\ForThumbHash::getThumbHash('thumbhash.jpg');
```

| Methode | Beschreibung | Parameter | Return |
| --- | --- | --- | --- |
| getThumbHash() | liefert den ThumbHash aus dem Medienpool, falls der ThumbHash noch nicht existiert wird er neu berechnet und im Medienpool gespeichert | string $mediafile | string $thumbhash |
| getThumbHashImg() | liefert das ThumbHash-Image aus dem Medienpool, falls das ThumbHash-Image noch nicht existiert wird es neu berechnet und im Medienpool gespeichert | string $mediafile | string $thumbhashimg |
| getThumbHashForFile() | liefert den ThumbHash aus der angegebenen Datei (Pfad) | string $path | string $thumbhash |
| getThumbHashImgForFile() | liefert das ThumbHash-Image aus der angegebenen Datei (Pfad) | string $path | string $thumbhashimg |
| getScript() | Liefert das Standard-Script zur Inline-Verwendung | void | string $script |
| getScriptTag() | Liefert das Standard-Script als Script-Tag | void | string $scripttag |
| createThumbHashes() | ThumbHashes für alle gültigen Bilder erstellen und im Medienpool speichern | void | int $count |
| clearThumbHashes()() | alle gespeicherten ThumbHashes löschen | void | string $error|true |

> **Hinweis:** Die beiden Methoden `getThumbHashForFile()` und `getThumbHashImgForFile()` können für Bilddateien ausserhalb des Medienpools verwendet werden. Hier muss der komplette Pfad angegeben werden.

## Empfehlungen / Sonstiges

* Bilder immer mit `width`m `height` `loading="lazy"`und ausgeben
* PHP-Klasse `ForThumbHash` für den Zugriff verwenden
* JavaScript am Seitenende _vor_ anderen Scripten einbinden

## Lizenz, Autor, Credits

### Lizenz

MIT Lizenz, siehe [LICENSE](https://github.com/FriendsOfREDAXO/thumbhash/blob/main/LICENSE)

ThumbHash.JS: [MIT Lizenz](https://github.com/evanw/thumbhash/blob/main/LICENSE.md)

ThumbHash.PHP: [MIT Lizenz](https://github.com/SRWieZ/thumbhash)

### Autor

Friends Of REDAXO
[https://github.com/FriendsOfREDAXO](https://github.com/FriendsOfREDAXO)

**Projekt-Lead**

Andreas Eberhard [@aeberhard](https://github.com/aeberhard)

### Credits

* Evan Wallace [@evanw](https://github.com/evanw) - https://evanw.github.io/thumbhash/ | https://github.com/evanw/thumbhash

* Eser DENIZ [@SRWieZ](https://github.com/SRWieZ) - https://github.com/SRWieZ/thumbhash

> Photo by [Marcelo Quinan](https://unsplash.com/de/@marceloquinan) from Unsplash: https://unsplash.com/de/fotos/u0ZgqJD55pE
