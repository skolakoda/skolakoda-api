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

$app->get('/', function() use($app) {
  return 'Zdravo';
});

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

  // ako mejl postoji azurirati korisnika, inace dodati
  // prijavu u prijave, datum upusuje default
  $provera_korisnika = $app['pdo']->prepare(
    "SELECT exists(SELECT 1 from korisnici where email='$email');"
  );

  $dodaje_korisnika = $app['pdo']->prepare(
    "INSERT INTO korisnici (ime, telefon, email) values ('$ime', '$telefon', '$email');"
  );

  $referer = $_SERVER['HTTP_REFERER'];
  return $provera_korisnika->execute();
});

/* START */

$app->run();
