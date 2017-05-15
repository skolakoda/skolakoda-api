<?php

require('../vendor/autoload.php');

$app = new Silex\Application();
$app['debug'] = true;

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));

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
  return $app['twig']->render('korisnici.twig', array(
    'korisnici' => $korisnici
  ));
});

$app->get('/kursevi', function() use($app) {
  $upit = $app['pdo']->prepare('SELECT * FROM kursevi');
  $upit->execute();
  $kursevi = array();
  while ($row = $upit->fetch(PDO::FETCH_ASSOC)) {
    $kursevi[] = $row;
  }
  return $app['twig']->render('kursevi.twig', array(
    'kursevi' => $kursevi
  ));
});

$app->get('/prijave', function() use($app) {
  $upit = $app['pdo']->prepare('SELECT * FROM prijave');
  $upit->execute();
  $prijave = array();
  while ($row = $upit->fetch(PDO::FETCH_ASSOC)) {
    $prijave[] = $row;
  }
  return $app['twig']->render('prijave.twig', array(
    'prijave' => $prijave
  ));
});

/* POST */

$app->post('/bilten', function() use($app) {
  $email = $_POST["email"];
  $referer = $_SERVER['HTTP_REFERER'];
  $upit = $app['pdo']->prepare("INSERT INTO korisnici (email) values ('$email');");
  $upit->execute();
  return "Email je sacuvan. Nazad na <a href='$referer'>$referer</a>";
});

$app->post('/prijava', function() use($app) {
  $ime = $_POST["ime"];
  $telefon = $_POST["telefon"];
  $email = $_POST["email"];
  $kurs = $_POST["kurs"];

  $provera_upit = $app['pdo']->prepare(
    "SELECT * FROM korisnici WHERE email='$email' LIMIT 1;"
  );
  $provera_upit->execute();
  $korisnik = $provera_upit->fetch(PDO::FETCH_ASSOC);

  if ($korisnik) {
    $upit_za_korisnika = "UPDATE korisnici SET (ime, telefon) = ('$ime','$telefon') WHERE email = '$email'";
  } else {
    $upit_za_korisnika = "INSERT INTO korisnici (ime, telefon, email) values ('$ime', '$telefon', '$email');";
    $provera_upit->execute();
    $korisnik = $provera_upit->fetch(PDO::FETCH_ASSOC);
  }

  $dodaja = $app['pdo']->prepare($upit_za_korisnika);
  $dodaja->execute();

  $korisnik_id = $korisnik['id'];
  $prijava = $app['pdo']->prepare(
    "INSERT INTO prijave (korisnik_id, kurs_id) values ('$korisnik_id', '$kurs');"
  );
  // TODO: proveriti jel vec postoji prijava
  $prijava->execute();

  // return "Hvala na prijavi! Nazad na <a href='$referer'>$referer</a>";
  return $korisnik_id;
});

/* START */

$app->run();
