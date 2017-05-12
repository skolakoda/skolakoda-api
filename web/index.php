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
  $app['monolog']->addDebug('logging output.');
  return str_repeat('Hello ', getenv('TIMES'));
});

$app->get('/korisnici', function() use($app) {
  $st = $app['pdo']->prepare('SELECT email FROM korisnici');
  $st->execute();
  $korisnici = array();
  while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
    $korisnici[] = $row;
  }
  return $app['twig']->render('korisnici.twig', array(
    'korisnici' => $korisnici
  ));
});

$app->get('/kursevi', function() use($app) {
  $st = $app['pdo']->prepare('SELECT * FROM kursevi');
  $st->execute();
  $kursevi = array();
  while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
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
  $st = $app['pdo']->prepare("insert into korisnici (email) values ('$email');");
  $st->execute();
  return "Email je sacuvan. Nazad na <a href='$referer'>$referer</a>";
});

$app->run();
