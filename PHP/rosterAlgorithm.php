<?php

// Die Funktion createRosterAlgorithm5() ist eine private Funktion der Klasse Roster ($this) und hat damit Zugriff auf die Kalender-Eingaben der Assistenten und die Daten aus der Team-Verwaltung

function createRosterAlgorithm5()
{
  if (!$this->assistanceInput->dataExist) {
    // wenn die Assistenten keine Termine eingetragen haben, kann kein Dienstplan erstellt werden
    return;
  }

  for ($i = 0; $i < $this->daysPerMonth; $i++) {
    // fuer jeden Tag des Monats wird verifziert, dass mindestens zwei Assistenten verfuegbar sind
    $countOfAvailableAssistants = 0;
    foreach ($this->assistanceInput->assistanceInput as $name => $dates) {
      if ($dates[$i] > 0) {
        $countOfAvailableAssistants++;
      }
    }
    if ($countOfAvailableAssistants < 2) {
      return;
    }
  }

  // Die Gesamtstunden aller Assistenten (die moegliche Daten eingegeben haben) werden zusammengerechnet
  $totalQuotaOfHours = 0;
  $quotaOfHours = $this->team->getHours();
  foreach ($quotaOfHours as $name => $value) {
    if (array_key_exists($name, $this->assistanceInput->assistanceInput)) {
      $totalQuotaOfHours += $value;
    }
  }

  // Die Gesamtstunden (Dienstzeiten + Bereitschaftszeiten) werden berechnet
  $totalOfServiceHours = 0;
  $totalOfStandbyHours = 0;
  for ($i = 1; $i <= $this->daysPerMonth; $i++) {
    $totalOfServiceHours += $this->monthPlan->days[$i]->serviceHours;
    $totalOfStandbyHours += $this->monthPlan->days[$i]->standbyHours;
  }

  // Der Skalierfaktor wird ermittelt
  $scaleFactor = ($totalOfServiceHours + $totalOfStandbyHours) / $totalQuotaOfHours;

  if ($scaleFactor < 1) {
    // Wenn der Skalierfaktor kleiner 1 ist, so wird er auf 1 korrigiert
    // Damit wird den Vorlieben des Klienten Vorrang vor einer gleichmaessigen Stunden-Kontingent-Ausschoepfung gewaehrt
    $scaleFactor = 1;
  }

  // Vorbereitung fuer die Berechnung der Punktetabelle
  $priorities = $this->team->getPriorities();
  $scoreTable = $this->assistanceInput->assistanceInput;
  $preferredWeekdays = $this->team->getPreferredWeekdays();
  $keyWords = $this->team->getKeyWords();

  foreach ($this->assistanceInput->assistanceInput as $name => $dates) {
    for ($i = 1; $i <= $this->daysPerMonth; $i++) {

      // allgemeine Vorlieben des Klienten werden beruecksichtigt
      $scoreTable[$name][$i - 1] *= $priorities[$name];

      // bevorzugte Wochentage werden beruecksichtigt
      if ($preferredWeekdays[$name][$this->monthPlan->days[$i]->weekday - 1] == 1) {
        $scoreTable[$name][$i - 1] *= 2;
      }

      // Stichwoerter werden beruecksichtigt
      foreach ($keyWords[$name] as $keyWord) {
        if ($keyWord != "") {
          if (strpos(strtolower($this->monthPlan->days[$i]->privateNotes), strtolower($keyWord)) !== false) {
            // wenn ein Assistenten-Stichwort in den privaten Bemerkungen des Tages enthalten ist, so wird der Prioritaetswert mit dem Faktor 10 multipliziert
            $scoreTable[$name][$i - 1] *= 10;
          }
        }
      }
    }
  }

  // Konvertierung der Punkte-Tabelle, so dass eine Betrachtung des gesamten Monats moeglich ist
  $convertedData = array();
  foreach ($this->assistanceInput->assistanceInput as $name => $dates) {
    for ($i = 1; $i <= $this->daysPerMonth; $i++) {
      if ($scoreTable[$name][$i - 1] == 0) {
        continue;
      }
      $entry = array();
      array_push($entry, $scoreTable[$name][$i - 1]);
      array_push($entry, $name);
      array_push($entry, $i);

      array_push($convertedData, $entry);
    }
  }

  $countOfRuns = 1000;
  $smallestDifference = PHP_INT_MAX;
  $servicePersonsBest = array();
  $standbyPersonsBest = array();

  // Erstellung von 1000 Dienstplaenen -> am Ende wird der beste gewaehlt
  for ($run = 0; $run < $countOfRuns; $run++) {

    shuffle($convertedData); // Randomisierung, damit es keine Block-Bildung bei den Diensten gibt
    usort($convertedData, 'compare'); // Sortierung nach Punkten - die compare-Funktion ist in diesem Listing nicht dargestellt

    // Dienstplan (vom vorherigen Durchlauf) loeschen
    for ($i = 1; $i <= $this->daysPerMonth; $i++) {
      $this->servicePerson[$i] = "";
      $this->standbyPerson[$i] = "";
    }

    // skalierte Stundenkontigente der Assistenten
    $quotaOfHours = $this->team->getHours($scaleFactor);

    // Bestimmung der Assistenten fuer die Dienste
    $serviceTolerance = 1;
    $serviceRun = 0;
    while (!$this->isServiceComplete()) { // Schleife, die solange laeuft, bis alle Dienste eingeteilt sind
      for ($i = 0; $i < count($convertedData); $i++) { // Schleife ueber alle Elemente der konvertierten Tabelle
        if ($this->servicePerson[$convertedData[$i][2]] == "") {// Pruefung, ob Dienst noch frei
          if ($quotaOfHours[$convertedData[$i][1]] - $this->monthPlan->days[$convertedData[$i][2]]->serviceHours >= 0 - ($serviceTolerance * $serviceRun)) { // Pruefung, ob Dienst noch in das Stundenkontigennt (+ Toleranz) reinpasst
            $this->servicePerson[$convertedData[$i][2]] = $convertedData[$i][1]; // Zuweisung des Dienstes
            $quotaOfHours[$convertedData[$i][1]] -= $this->monthPlan->days[$convertedData[$i][2]]->serviceHours; // Abziehen der Stunden des zugewiesenen Dienstes von Stundenkontingent
          }
        }
      }
      // Inkrementierung des Durchlauf-Zaehlers, damit die Stunden Toleranz beim naechsten Durchlauf eine Stunde groesser ist
      $serviceRun++;
    }

    // Bestimmung der Assistenten fuer die Bereitschaften
    // Vorgehensweise analog zur Bestimmung der Assistenten fuer die Dienste
    $standbyTolerance = 0.5;
    $standbyRun = 0;
    while (!$this->isStandbyComplete()) {
      for ($i = 0; $i < count($convertedData); $i++) {
        if ($this->standbyPerson[$convertedData[$i][2]] == "" && $this->servicePerson[$convertedData[$i][2]] != $convertedData[$i][1]) {
          if ($quotaOfHours[$convertedData[$i][1]] - $this->monthPlan->days[$convertedData[$i][2]]->standbyHours >= 0 - ($standbyTolerance * $standbyRun)) {
            $this->standbyPerson[$convertedData[$i][2]] = $convertedData[$i][1];
            $quotaOfHours[$convertedData[$i][1]] -= $this->monthPlan->days[$convertedData[$i][2]]->standbyHours;
          }
        }
      }
      $standbyRun++;
    }

    // Bestimmung der "Metrik" des gerade eben erstellten Dienstplans
    $currentDifference = $serviceRun * $serviceTolerance + $standbyRun * $standbyTolerance;

    if ($currentDifference < $smallestDifference) { // Pruefung ob aktueller Dienstplan besser als bisher bester
      // kleinste Differenz aktualisieren und neuerstellten Dienstplan speichern
      $smallestDifference = $currentDifference;
      $servicePersonsBest = $this->servicePerson;
      $standbyPersonsBest = $this->standbyPerson;
    }
  }

  // Speicherung des besten Dienstplans
  $this->servicePerson = $servicePersonsBest;
  $this->standbyPerson = $standbyPersonsBest;
}
?>