# Permission

## Grundidee
In einer main.law-Datei werden - als XML formatiert - die grundlegenden Dinge definiert. Es wird definiert, welche
Gruppen existieren, welche Rechte es gibt und welche Standardrechte vorhanden sind.

Die XML-Datei ist wie folgt aufgebaut:

law
    -> groups
        -> group:       name    level
        -> ...
    -> rules:           to
        -> cat:         name
            -> item     name    value
            -> ...
        -> ...

Zu jeder Gruppe kann eine *.group-Datei definiert werden. Diese Datei beinhaltet alle weiteren Rechte, die nicht in der
main.law-Datei definiert werden.

Die Idee ist es, nur grundlegende Rechte - sprich Rechte, die für alle gleich sind - in der main.law zu definieren. Die
main.law überschreibt im jeden Falle die *.group-Datei. Die Werte, bei denen to=* sind, überschreiben in jedem Falle
alle anderen Werte.

Die *.group-Datei ist wie folgt aufgebaut:

permission
    -> group:           name
        -> cat:         name
            -> item     name    value
            -> ...
        -> ...
    -> ...

Wobei in jeder *.group-Datei mehrere Gruppen definiert werden können. Dabei gilt, dass das jeweils erstgenannte Recht
definiert wird. Sprich: Wenn ein Eintrag doppelt vorhanden ist, wird der, der weiter oben steht gespeichert und der
untere ignoriert.

## Beispiel eines Einlesevorganges
```
$Permission = new Permission;
$Permission->useLaw('main.law');
$Permission->useGroup('root.group');
```

In diesem Beispiel wird zuerst die main.law-Datei eingelesen und danach die root.group-Datei. Sollte eine .group-Datei vor
der .law-Datei eingelesen werden, so wird ein Fehler erzeugt.

### group level
Jede Gruppe erhält in der .law-Datei ein Attribut "level". Dabei gilt, dass je niedriger dieser Wert ist (min=0;max=max(int)),
desto mehr "Wert" hat ein Recht dieser Gruppe.

Jeder Benutzer kann mehreren Gruppen zugewiesen werden, wobei die Rechte der höher gewerteten Gruppe mehr gelten als die
der niedrigeren Gruppe. Dabei spielt es keine Rolle, in welcher Reihenfolge die Rechte eingelesen oder definiert werden.

