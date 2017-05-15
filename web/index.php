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
  $upit = $app['pdo']->prepare("INSERT INTO korisnici (email) values ('$email');");
  $upit->execute();

  $referer = $_SERVER['HTTP_REFERER'];
  return "Email je sacuvan. Nazad na <a href='$referer'>$referer</a>";
});

$app->post('/prijava', function() use($app) {
  $ime = $_POST["ime"];
  $telefon = $_POST["telefon"];
  $email = $_POST["email"];
  $kurs = $_POST["kurs"];

  $azurira_korisnika = "UPDATE korisnici
    SET (ime, telefon) = ('$ime','$telefon')
    WHERE email = '$email';
  ";
  $unosi_korisnika = "INSERT INTO korisnici
    (ime, telefon, email)
    values ('$ime', '$telefon', '$email')
    RETURNING id;
  ";

  /* INIT */

  $provera_korisnika = $app['pdo']->prepare(
    "SELECT * FROM korisnici WHERE email='$email' LIMIT 1;"
  );
  $provera_korisnika->execute();
  $korisnik = $provera_korisnika->fetch(PDO::FETCH_ASSOC);
  $korisnik_id = $korisnik['id'];

  if ($korisnik) {
    $upit = $app['pdo']->prepare($azurira_korisnika);
    $upit->execute();
  } else {
    $upit = $app['pdo']->prepare($unosi_korisnika);
    $upit->execute();
    $korisnik_id = $app['pdo']->lastInsertId();
  }

  $prijava = $app['pdo']->prepare(
    "INSERT INTO prijave (korisnik_id, kurs_id) values ('$korisnik_id', '$kurs');"
  );
  // proveriti jel vec postoji prijava!
  $prijava->execute();

  $referer = $_SERVER['HTTP_REFERER'];
  return "Hvala na prijavi! Nazad na <a href='$referer'>$referer</a>";
});

/* START */

$app->run();
