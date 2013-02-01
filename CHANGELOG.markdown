# KLIT FRAMEWORK CHANGELOG

## Wie funktioniert die Nummerierung der Versionen?
Die Nummerierung ist etwas eigenartig, aber durchaus sinnvoll.

Grundsätzlich wird eine neue Version aus einem Klon der alten gebildet. Dabei bleiben während der Entwicklungsphase alle Versionsnummern bestehen. Allerdings wird bei jedem Commit eine Revisionsnummer (@revision) um eins (1) erhöht. Sobald die Entwicklung fertig gestellt ist, wird danach die Versionsnummer angepasst und die Revisionsnummer entfernt.

Dabei gilt folgendes "Gesetz":
1.0.0
| | \
| |  - Wird erhöht, falls nur kleinere Anpassungen vorgenommen wurden (Bugs etc.)
|  \
|   - Wird erhöht, falls der Funktionsumfang angepasst wurde, aber Abwärtskompilität gewährleistet ist.
 \
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
    <tr>
        <td rowspan="2">\Lib\Query\Query.class.php</td>
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
    <tr>
        <td>\Lib\Database\MySQL.class.php</td>
        <td>3.0.1</td>
        <td>01</td>
        <td>
            now singleton
        </td>
    </tr>
    <tr>
        <td>\Lib\Path\Path</td>
        <td>1.0.0</td>
        <td>01</td>
        <td>
            removed bootstrap
        </td>
    </tr>
    <tr>
        <td>\Config\Constant</td>
        <td>1.0.0</td>
        <td>01</td>
        <td>
            added PATH_CONTROLLER
        </td>
    </tr>
    <tr>
        <td>\Config\Controller</td>
        <td>1.0.0</td>
        <td>01</td>
        <td>
            added steady constants
        </td>
    </tr>
    <tr>
        <td>\Core\Controller</td>
        <td>1.0.0</td>
        <td>01</td>
        <td>
            added steady methods
        </td>
    </tr>
    <tr>
        <td>\Src\Steady\Steady</td>
        <td>1.0.0</td>
        <td>01</td>
        <td>
            added class
        </td>
    </tr>
</table>
