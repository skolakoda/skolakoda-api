<?php

require('../vendor/autoload.php');

$app = new Silex\Application();
$app['debug'] = true;

// Register the monolog logging service
$app->register(new Silex\Provider\MonologServiceProvider(), array(
  'monolog.logfile' => 'php://stderr',
));

// Register view rendering
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));

$dbopts = parse_url(getenv('DATABASE_URL'));
$app->register(new Herrera\Pdo\PdoServiceProvider(),
               array(
                   'pdo.dsn' => 'pgsql:dbname='.ltrim($dbopts["path"],'/').';host='.$dbopts["host"] . ';port=' . $dbopts["port"],
                   'pdo.username' => $dbopts["user"],
                   'pdo.password' => $dbopts["pass"]
               )
);

/* RUTE */

$app->get('/', function() use($app) {
  $app['monolog']->addDebug('logging output.');
  return str_repeat('Hello ', getenv('TIMES'));
});

$app->get('/db/', function() use($app) {
  $st = $app['pdo']->prepare('SELECT email FROM korisnici');
  $st->execute();

  $korisnici = array();
  while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
    $app['monolog']->addDebug('Row ' . $row['email']);
    $korisnici[] = $row;
  }

  return $app['twig']->render('database.twig', array(
    'korisnici' => $korisnici
  ));
});

$app->post('/prijava', function() use($app) {
  $email = $_POST["email"];
  $st = $app['pdo']->prepare("insert into korisnici (email) values ('$email');");
  $st->execute();
  return "Email je sacuvan. Nazad na $_SERVER['HTTP_REFERER']";
});

$app->run();
