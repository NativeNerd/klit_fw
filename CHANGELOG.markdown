# KLIT FRAMEWORK CHANGELOG

## Wie funktioniert die Nummerierung der Versionen?
Die Nummerierung ist etwas eigenartig, aber durchaus sinnvoll.

Grundsätzlich wird eine neue Version aus einem Klon der alten gebildet. Dabei bleiben während der Entwicklungsphase alle Versionsnummern bestehen. Allerdings wird bei jedem Commit eine Revisionsnummer (@revision) um eins (1) erhöht. Sobald die Entwicklung fertig gestellt ist, wird danach die Versionsnummer angepasst und die Revisionsnummer entfernt.

Dabei gilt folgendes "Gesetz":

hinterste Stelle
- Wird erhöht, falls nur kleinere Anpassungen vorgenommen wurden (Bugs etc.)
mittlere Stelle
- Wird erhöht, falls der Funktionsumfang angepasst wurde, aber Abwärtskompilität gewährleistet ist.
vorderste Stelle
- Wird erhöht, falls die Klasse nicht mehr abwärts kompatibel ist.

Dasselbe gilt auch sinnbildlich für die Versionsnummern des gesamten Frameworks.

## Version 0.3 (parent 0.2)
<table>
    <tr>
        <th>File</th>
        <th>Version</th>
        <th>File revision</th>
        <th>What changed</th>
    </tr>
<!-- /Lib/Query/Query.class.php -->
    <tr>
        <td rowspan="2">/lib/query/Query.class.php</td>
        <td>2.0.0</td>
        <td>01</td>
        <td>
            added ON() statement<br />
            added IN() statement<br />
        </td>
    </tr>
    <tr>
        <td>2.0.0</td>
        <td>02</td>
        <td>
            not anymore a singleton
        </td>
    </tr>
<!-- /lib/database/MySQL.class.php -->
    <tr>
        <td>/Lib/Database/MySQL.class.php</td>
        <td>3.0.1</td>
        <td>01</td>
        <td>
            now singleton
        </td>
    </tr>
<!-- /Lib/Path/Path.class.php -->
    <tr>
        <td rowspan="2">/Lib/Path/Path.class.php</td>
        <td>1.0.0</td>
        <td>01</td>
        <td>
            removed bootstrap
        </td>
    </tr>
    <tr>
        <td>1.0.0</td>
        <td>02</td>
        <td>
            added build path to exception message
        </td>
    </tr>
<!-- /config/Constant.config.php -->
    <tr>
        <td>/Config/Constant</td>
        <td>1.0.0</td>
        <td>01</td>
        <td>
            added PATH_CONTROLLER
        </td>
    </tr>
<!-- /config/Controller.config.php -->
    <tr>
        <td>/config/Controller</td>
        <td>1.0.0</td>
        <td>01</td>
        <td>
            added steady constants
        </td>
    </tr>
<!-- /core/Controller.class.php -->
    <tr>
        <td rowspan="2">/core/Controller</td>
        <td>1.0.0</td>
        <td>01</td>
        <td>
            solved a bug with loading steady class
        </td>
    </tr>
    <tr>
        <td>1.0.0</td>
        <td>01</td>
        <td>
            added steady methods
        </td>
    </tr>
<!-- /src/controller/steady/steady.controller.php -->
    <tr>
        <td rowspan="2">/src/controller/Steady.controller.php</td>
        <td>1.0.0</td>
        <td>01</td>
        <td>
            added class
        </td>
    </tr>
    <tr>
        <td>1.0.0</td>
        <td>02</td>
        <td>
            solved a bug with class name
        </td>
    </tr>
<!-- /lib/template/Template.class.php -->
    <tr>
        <td rowspan="2">/lib/template/template.class.php</td>
        <td>1.0.0</td>
        <td>00</td>
        <td>
            Integrated Twig
        </td>
    </tr>
    <tr>
        <td>1.0.0</td>
        <td>01</td>
        <td>
            added ability to auto-load functions
        </td>
    </tr>
</table>
