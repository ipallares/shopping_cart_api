# Cart API
Es gibt 2 Branches in diesem Repo. Auf `overengineered_approach` wollte ich infrastructure von Domain and Application layers entkoppeln (am meistens von Doctrine) um in der Clean/Hexagonal Architecture zu gehen. 
Es war mir bewusst, das für eine solche API dieser Ansatz zu viel war (deswegen den Branch Name :) ), wollte ich aber ausprobieren. Leider müsste ich aufhören, weil ich nicht genug Zeit dafür hatte, können wir aber darüber reden (warum ich es so ausprobiert habe, warum ich einige Entscheidungen getroffen habe, was würde ich anders machen...).

Der andere Branch ist `mvc_approach` und da habe ich den API bis zum Ende implementiert. Es gibt en paar disclaimers und Verbesserungen, die, wenn ich mehr Zeit hatte, da machen würde.
* Mehr Exceptions implementieren. Ich benutze allgemeine Exceptions (`ResourceNotFoundException`, `InvalidArgumentException`...), wäre es aber gut spezifischer Exception anzulegen.
* Ich benutze un einzelne Schema für Cart Create und Update, beider für Request und Response. Man sollte unterschiedliche Schemas für jeden Fall benutzen weil Json strings unterschiedlich sein könnten/sollten.
* Ordner könnten ein bisschen besser Cart und CartProducts trennen.
* Mit Cart endpoints man kann:
	* Einen leer Cart anlegen
	* Einen Cart mit Products anlegen.
	* Einen Cart updaten (Products im Cart werden durch den bekommenen Json Body ersetzt).
	* Den Cart details sehen.
* Mit CartProduct endpoints man kann:
	* Ein Product in einem Cart hinzufügen.
		* Wenn das Product noch nicht im Cart ist, dann wird es mit der gegebene Anzahl hinzugefügt.
		* Wenn das Product schon im Cart ist, werden die Anzahle summiert.
	* Ein Product von einem Cart löschen (wenn man die Anzahl des Produktes verändern, muss man zuerst löschen und danach wieder mit der neue Quantität das Product hinzufügen).
	* Wenn ich mehr Zeit hätte, würde ich converters von Cart Entity zu Json Object schreiben (erstmal gibt es nur die Umwandlung to Json String und dann muss man das zu Object umwandeln mit `json_decode`).
* Wäre schön gewesen Integration tests zu schreiben.
* Im Prinzip sollten Services nicht mehr public sein (das war so, nur weil als ich sie getestet habe, wurden sie noch nicht autowired und so noch nicht zu Verfügung). Davor möchte ich sicher machen, dass das kein Problem verursacht, habe ich aber erstmal leider keine Zeit das richtig zu testen. 

## Installation
Einmal man den Repo cloned und `mvc_approach` Branch auschecked, muss man folgende Schritte machen:

 1. `composer install`
 2. `touch var/data.db`
 3. `php bin/console doctrine:migrations:migrate`
 4. `php bin/console doctrine:fixtures:load`. Optional, nur damit man irgendwelche Info in DB hat.
 5. `symfony server:start` Da steht die URL, wo man die Application finden kann

### Tests 

 1. `touch var/data-test.db`
 2. `./bin/phpunit`

Es kann sein, dass symfony/phpunit-bridge wieder installiert werden muss (wenn man kein bin/phpunit Date findet. in solchem fall:
```
composer rem --dev symfony/phpunit-bridge
composer req --dev symfony/phpunit-bridge
```
Tests funktionieren mit DB `var/data-test.db`



