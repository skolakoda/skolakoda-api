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

$app->post('/prijava', function() use($app) {
  $email = $_POST["email"];
  $referer = $_SERVER['HTTP_REFERER'];
  $upit = $app['pdo']->prepare("insert into korisnici (email) values ('$email');");
  $upit->execute();
  return "Email je sacuvan. Nazad na <a href='$referer'>$referer</a>";
});

$app->run();
