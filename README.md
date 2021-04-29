Dieses Plugin fügt eurem Forum einen Mini-Kalender hinzu, der im Header (oder Footer) des Forums ausgegeben werden kann. 

Der vollständige Kalender des Jahres kann über inplaykalender.php erreicht werden. Über inplaykalender.php?y=2020 können vergangene oder kommende Jahre (hier z.B. 2020) erreicht werden. 

<h1>Funktionen</h1>

<ul>
<li> Anzeige von Informationen aus dem Inplaytracker 3.0
<li> Anzeige von Informationen aus dem Plottracker
<li> Anzeige von Geburtstagen
<li> Hinzufügen von eigenen Events [z.B können User Events wie Partys einfügen, die keinen Plot "wert" sind)
<li> Verteilen von Berechtigungen, welche Gruppen Events hinzufügen können
<li> Farbliche Anzeige der Events im Header inkl. Legende im Kalender
<li> Eigene Seite: inplaykalender.php
<li> Hinterlegen von Spieljahr und bespielten Monaten im Admin CP
</ul>

<h1>Neue Templates</h1>
Es wird folgende Templates neu hinzugefügt, ihr findet sie in den globalen Templates: 

<ul>
<li>header_inplaykalender
<li>header_inplaykalender_bit
<li>inplaykalender
<li>inplaykalender_add
<li>inplaykalender_day_bit
<li>inplaykalender_day_bit_popup
<li>inplaykalender_month_bit
<li>inplaykalender_nav 
<li>inplaykalender_nav_add 
<li>inplaykalender_no_day_bit
</ul>

<h1>Templateänderungen</h1>
Dem Header-Template wird die Variable {$header_inplaykalender} hinzugefügt. Sie zeigt den Kalender oben im Header an.

<h1>Neues CSS</h1>
All' euren Designs wird ein inplaykalender.css hinzugefügt. 

<h1>Demo</h1>

<center>
  <a href="https://snipboard.io/7IUOre.jpg"><img src="https://snipboard.io/7IUOre.jpg" /></a>

<img src="https://snipboard.io/97UJLk.jpg" />

<img src="https://snipboard.io/HRA2cE.jpg" />

<a href="https://belle.eightletters.de">Live Demo</a></center>

<h1>Inplayzeitraum eintragen</h1>
Den Inplayzeitraum tragt ihr im ACP unter <b>Konfiguration &bull; Inplaykalender Einstellungen</b> ein. Gebt eure Spielmonate mit Komma getrennt an (kein Leerzeichen dazwischen)! Die richtige Angabe ist <b>sehr wichtig</b>. <br /><br />

Was, wenn ihr zum Jahreswechsel spielt? Zum Beispiel im November und Dezember des Jahres 2020, aber im Januar 2021? Ganz einfach: ihr könnt Inplaymonate auch wiefolgt angeben: <b>November 2020,Dezember 2020,Januar 2021</b> (auch hier kein Leerzeichen zwischen den Kommata!). Der Kalender auf der Kalender-Hauptseite inplaykalender.php wird davon ab immer das Jahr anzeigen, das ihr als Spieljahr im ACP eingetragen habt. 

Ihr könnt beliebig viele Monate anzeigen lassen - ich empfehle aber maximal drei. ;) 

Viel Spaß.
