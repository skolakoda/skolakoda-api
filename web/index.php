<?php

require('../vendor/autoload.php');

$app = new Silex\Application();
$app['debug'] = true;

$dbopts = parse_url(getenv('DATABASE_URL'));
$app->register(
  new Herrera\Pdo\PdoServiceProvider(),
   array(
       'pdo.dsn' => 'pgsql:dbname='.ltrim($dbopts["path"],'/').';host='.$dbopts["host"] . ';port=' . $dbopts["port"],
       'pdo.username' => $dbopts["user"],
       'pdo.password' => $dbopts["pass"]
   )
);

/* GET */

$app->get('/korisnici', function() use($app) {
  $upit = $app['pdo']->prepare('SELECT * FROM korisnici');
  $upit->execute();
  $korisnici = array();
  while ($row = $upit->fetch(PDO::FETCH_ASSOC)) {
    $korisnici[] = $row;
  }
  return json_encode($korisnici);
});

$app->get('/kursevi', function() use($app) {
  $upit = $app['pdo']->prepare('SELECT * FROM kursevi');
  $upit->execute();
  $kursevi = array();
  while ($row = $upit->fetch(PDO::FETCH_ASSOC)) {
    $kursevi[] = $row;
  }
  return json_encode($kursevi);
});

$app->get('/prijave', function() use($app) {
  $upit = $app['pdo']->prepare('SELECT * FROM prijave');
  $upit->execute();
  $prijave = array();
  while ($row = $upit->fetch(PDO::FETCH_ASSOC)) {
    $prijave[] = $row;
  }
  return json_encode($prijave);
});

/* POST */

$app->post('/bilten', function() use($app) {
  $email = $_POST["email"];
  $upit = $app['pdo']->prepare("INSERT INTO korisnici (email) values ('$email');");
  $upit->execute();

  $referer = $_SERVER['HTTP_REFERER'];
  return "Email je sacuvan. Nazad na <a href='$referer'>$referer</a>";
});

$app->post('/prijava', function() use($app) {
  $referer = $_SERVER['HTTP_REFERER'];
  $ime = $_POST["ime"];
  $telefon = $_POST["telefon"];
  $email = $_POST["email"];
  $kurs = $_POST["kurs"];
  $uzivo = $_POST["uzivo"];

  $azurira_korisnika = "UPDATE korisnici
    SET (ime, telefon, prijavljen) = ('$ime','$telefon', TRUE)
    WHERE email = '$email';
  ";
  $unosi_korisnika = "INSERT INTO korisnici
    (ime, telefon, email, prijavljen)
    values ('$ime', '$telefon', '$email', TRUE)
    RETURNING id;
  ";

  $provera_korisnika = $app['pdo']->prepare(
    "SELECT * FROM korisnici WHERE email='$email' LIMIT 1;"
  );
  $provera_korisnika->execute();
  $korisnik = $provera_korisnika->fetch(PDO::FETCH_ASSOC);
  $korisnik_id = $korisnik['id'];

  if ($korisnik) {
    $app['pdo']->prepare($azurira_korisnika)->execute();
  } else {
    $app['pdo']->prepare($unosi_korisnika)->execute();
    $korisnik_id = $app['pdo']->lastInsertId();
  }

  $provera_prijave = $app['pdo']->prepare(
    "SELECT * FROM prijave WHERE korisnik_id='$korisnik_id' AND kurs_id='$kurs' AND uzivo='$uzivo' LIMIT 1;"
  );
  $provera_prijave->execute();
  $ranija_prijava = $provera_prijave->fetch(PDO::FETCH_ASSOC);
  $prijava_id = $ranija_prijava['id'];

  if ($ranija_prijava) {
    return "Vec ste prijavljeni na ovaj kurs! Nazad na <a href='$referer'>$referer</a>";
  } else {
    $prijava = $app['pdo']->prepare(
      "INSERT INTO prijave (korisnik_id, kurs_id, uzivo) values ('$korisnik_id', '$kurs', '$uzivo');"
    );
    $prijava->execute();
    return "Hvala na prijavi! Nazad na <a href='$referer'>$referer</a>";
  }
});

$app->post('/brisanje', function() use($app) {
  $prijava_id = $_POST["$prijava_id"];
  return "Obrisano" . $prijava_id;
  // $upit = $app['pdo']->prepare("INSERT INTO korisnici (email) values ('$email');");
  // $upit->execute();
  //
  // $referer = $_SERVER['HTTP_REFERER'];
  // return "Email je sacuvan. Nazad na <a href='$referer'>$referer</a>";
});

/* START */

$app->run();
