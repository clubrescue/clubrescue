![alt tag](https://raw.github.com/BorghoutsR/ClubRedders/master/images/cr.gif)
===========

[ClubRedders](https://raw.github.com/BorghoutsR/ClubRedders), a web-app based on material design

### Current Version : v0.8.6

You need to place the following file/directories outside the public served folder:
env.ini
crbin/backup
crbin/excel
crbin/import
crbin/pasfotos
crbin/pasfotos/delete

## Supported Browsers:
Chrome 80+, Edge 80+, Firefox 74+, Safari 13+

## Changelog
Bolded styling surrounded by emojis indicates the major release focus.
  CRV	 Status		 Date		Release series	Released by
- v0.8.6 RC (6)		(t.b.d.)	Beluga 600		Ruud Borghouts
  - **Online portal op basis van CSS design framework for mobile apps**
  - Materialize CSS Framework v0.100.1 implemented as new front-end for Club.Redders.
  - Updated .htaccess for web-authentication only with restrictive deny.
  - My C.R envoironment is now also available inside C.R (previously only when included to your website).
- v0.8.5 RC (5)		(3/28/2017)	Beluga 600		Ruud Borghouts
  - **Product modulaire opbouw**
  - Online portal is modulair opgezet ipv een enkele php file voor meer flexibiliteit.
- v0.8.4 RC (4)		(3/13/2017)	Beluga 600		Ruud Borghouts
  - **Product breede web-authenticatie**
  - Authenticatie tegen WP ipv .htaccess/.htpasswd bestanden
- v0.8.3 RC (3)		(6/23/2016)	Beluga 600		Ruud Borghouts
  - **Weekindeling**
  - EBT kan nu weekindelingen printen voor de ASC en 809.
  - Alle output bestanden hebben een vergelijkbare layout.
  - Excel authenticatie tegen online portal en backup procedure verbeterd.
  - Print opdrachten worden nu allemaal voorzien van een standaard naamgeving.
  - Eerste set van de nieuwe proces/werkinstructies toegevoegd aan de release package.
- v0.8.1 RC (2)		(6/14/2016)	Beluga 600		Ruud Borghouts
  - **Online portal**
  - EBT op basis van online Sportlink data upload en automatische online EBT import.
  - Gebruikershandleiding is naar een eigen set documenten verplaatst.
  - Versie nummering is uitgebreid naar 3 posities.
- v0.8.0 RC (1)		(6/2/2016)	Beluga 600		Ruud Borghouts
  - **Bron data gemigreerd naar Sportlink én competentiekaarten module**
  - EBT op basis van Sportlink data via een handmatige Sportlink .csv export en automatische EBT import.
  - Interne data (uit de vorige versies op basis van ST-RBM) is verwijderd, inclusief bijbehorende tabbladen.
  - Berekening van interne rollen en diploma's vind nu plaats op de achtergrond i.p.v. bij de postindeling.
  - Competentiekaarten module toegevoegd welke voor alle bewakers een competentiekaart genereerd.
  - Macro knop toegevoegd waarmee de competentiekaarten worden geprint.
- v0.7.0 Beta		(8/7/2015)	Tonijn			Ruud Borghouts
  - **Gebruik postindeling verbeterd**
  - Posities gelijk gemaakt aan de post, het volgnummer word nu op de achtergrond geregeld.
  - Sorterings issue opgelost bij gelijke ervaring én leeftijd (of ontbreken van deze data).
  - Macro knop toegevoegd waarmee de externe data word vernieuwd en alles word herberekend.
  - Macro knop toegevoegd waarmee de planning als PDF word opgeslagen.
  - Macro knop toegevoegd waarmee de verzendlijst voor de enquete van die week word aangemaakt.
  - Macro knop toegevoegd waarmee de indeling en enquete verzonden worden naar de SL beheerders.
  - Macro knop toegevoegd om de applicatie modus te testen (in ontwikkeling).
  - Macro toegevoegd die bij openen en afsluiten het document optimaliseerd.
  - SplashScreen toegevoegd tijdens de voorberekeningen van het document bij openen.
  - Release data om tabblad Info verbeterd en uitgebreid met geplande features.
- v0.6.0 Pre-Beta	(7/22/2015)	Development		Ruud Borghouts
  - **Postindeling automatisch gesorteerd**
  - Posities worden gevuld op basis van meest naar minste weken ervaring.
  - Op het tabblad Bron-Commissie ook een hulpkolom voor leeftijd in dagen toegevoegd om bij gelijke ervaring te sorteren op leeftijd.
  - Indien iemand bewaakt en (geen) pasfoto heeft word de achtergrond grijs gekleurd.
  - Toelichting toegevoegd op het tabblad info.
- v0.5.0 A-Bug-fix	(7/14/2015)	Development		Ruud Borghouts
  - **Bug-fixes doorgevoerd t.a.v gebruik**
  - Controle op dubbele postposities toegevoegd.
  - Macroscript aangepast zodat ook de positie 28.7 in de nieuwe layout van een pasfoto word voorzien.
- v0.4.0 Alpha		(7/11/2015)	Development		Ruud Borghouts
  - **Nieuwe layout**
  - Nieuwe layout (nieuw tabblad Postindeling/PIv1) doorgevoerd i.v.m. de leesbaarheid van de postindelingen.
- v0.3.0 Pre-Alpha	(7/1/2015)	Development		Ruud Borghouts
  - **Erv. afronden technisch verbeterd**
  - Gemiddelde ervaring per post word nu afgerond weergegeven in de postindeling.
- v0.2.0 Init VBA	(6/27/2015)	Development		Ruud Borghouts
  - **Data website aangepast op script**
  - Namen van de pasfoto's gecorrigeerd op de webserver zodat deze juist ingeladen worden door het script.
  - De celindeling van het rooster is veranderd tussen de demo en versie 0.1, deze is nu doorgevoerd in het macroscript voor de pasfotos. Bewakers week 27 aangepast in de bron data.
- v0.1.0 Init rel.	(6/26/2015)	Development		Ruud Borghouts
  - **Initiele EBT voor het maken en printen van de bewakingsroosters op basis van Excel met oude (Access) DB data**
  - Postindeling invullen incl. foto op basis van leden.trb.nu
  - Sportlink-EBT vergelijking maken, wie zit in SportLink en niet in de EBT data en vica versa.
  - Ervaring word geteld t/m seisoen 2014 (formule moet (nog) elke week worden aangepast voor actuele erv.).
  - Deze tool is de basis voor een database registratie in SportLink die de roosters en competentiekaarten uitprint via een csv export in/met Excel.
- Demo Concept		(5/29/2015)	Development		Ruud Borghouts
  - **Mock-up indeling/compkaart systeem**
  - Demonstratie geven van de mogelijkheden van een rooster/competentiekaart generator op basis van Excel.

## Planned feature releases
Bolded styling surrounded by emojis indicates the major release focus.
  CRV	 Status		 Date		Release series	Released by
- v0.8.7 RC (7)		(t.b.d.)	Beluga 600		Ruud Borghouts
  - **Online portal uitbreiding met installatie/update script**
- v0.8.8 RC (8)		(t.b.d.)	Beluga 600		Ruud Borghouts
  - **Alle waardes zijn variabel, te configureren in online configuratie bestanden**
- v0.8.9 RC (9)		(t.b.d.)	Beluga 600		Ruud Borghouts
  - **Bux-fixes uit voorgaande release candidates**
- v0.9.0 RTM		(t.b.d.)	Leng			Ruud Borghouts
  - **Volledig online configureerbare release welke automatisch kan updaten**
- v1.0.0 GA			(t.b.d.)	Zalm			Ruud Borghouts
  - **Bug-fixes uit de RTM, update zou automatisch moeten kunnen uitrollen**

## Sitemap
   clubredders/
   |--css/
   |  |--style.css
   |  |--materialize.css
   |  |--materialize.min.css
   |
   |--fonts/
   |  |--roboto/
   |
   |--js/
   |  |--init.js
   |  |--materialize.js
   |  |--materialize.min.js
   |
   |--util/						Map met functie code.
   |
   |--README.txt				Dit document met basis informatie en versie historie.
   |--index.php					Club.Redders core waarvandaan de modules gepresenteerd worden op basis van de ingelogde gebruiker.
   |--slimport.php				Module voor het inlezen van csv bestanden uit Sportlink.
   |--pasfoto.php				Module voor het uploaden en bijsnijden van een pasfoto door het lid zelf.
   |--pasfotos.php				Module voor het uploaden van meerdere pasfotos van leden door het kader.
   |--edp-leden.php				ExterneDataConnector, leest de ingelezen csv data uit de C.R database en maakt deze beschikbaar voor de C.R ExcelDataModule.
   |--edp-diplomas.php			ExterneDataConnector, leest de ingelezen csv data uit de C.R database en maakt deze beschikbaar voor de C.R ExcelDataModule.
   |--edp-indeling.php			ExterneDataConnector, leest de ingelezen csv data uit de C.R database en maakt deze beschikbaar voor de C.R ExcelDataModule.
   |--wp-authenticate.php		Security module voor WordPress authenticatie.

   crbin/                       Folder met binaire bestanden die buiten de public_html folder moet worden geplaatst.
   |--backup/					Map voor backups van vorige Excel tools, CSV imports en pasfotos.
   |--excel/					Map voor Excel tools.
   |--import/					Map voor CSV uploads.
   |--pasfotos/					Map voor pasfotos uploads.

