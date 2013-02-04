<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jsundquist
 * Date: 1/31/13
 * Time: 6:45 PM
 * To change this template use File | Settings | File Templates.
 */

use Symfony\Component\HttpFoundation\Response;

require_once __DIR__ . '/../vendor/autoload.php';

$navigation = array(
    array('title' => 'Home', 'link' => '/'),
    array('title' => 'Register', 'link' => '/register'),
    array('title' => 'Sessions', 'link' => '/sessions'),
//    array('title' => 'Schedule', 'link' => '/schedule'),
    array('title' => 'Speakers', 'link' => '/speakers'),
    array('title' => 'Sponsors', 'link' => '/sponsorList'),
    array('title' => 'Venue', 'link' => '/venue'),
    array('title' => 'Contact', 'link' => '/contact'),
);

$app = new Silex\Application();

$app['debug']=true;

require_once __DIR__ . "/../includes/database.php";

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/../views/',
));
$app->get("/", function (Silex\Application $app) use ($navigation) {
    return $app['twig']->render('index.html.twig', array(
        'nav' => $navigation,
        'active' => 'Home'
    ));
});

$app->get("/badges", function (Silex\Application $app) use ($navigation) {
    return $app['twig']->render('badges.html.twig', array(
        'nav' => $navigation,
        'active' => 'Home'
    ));
});

$app->get("/banners", function (Silex\Application $app) use ($navigation) {
    return $app['twig']->render('banners.html.twig', array(
        'nav' => $navigation,
        'active' => 'Home'
    ));
});

$app->get("/register", function (Silex\Application $app) use ($navigation) {
    return $app['twig']->render('register.html.twig', array(
        'nav' => $navigation,
        'active' => 'Register'
    ));
});
$app->get("/sessions", function (Silex\Application $app) use ($navigation) {

    $sqlStatement = "SELECT id, fname, lname, summary FROM c4p WHERE status = 'accepted' ORDER BY track, title ASC";

    $sessions = $app['dbs']['mysql_read']->fetchAll($sqlStatement);

    return $app['twig']->render('sessions.html.twig', array(
        'nav' => $navigation,
        'active' => 'Home',
        'sessions' => $sessions
    ));
});

$app->get("/sessions/{id}", function (Silex\Application $app, $id) use ($navigation) {

    $sqlStatement = "SELECT * FROM c4p WHERE status = 'accepted' ORDER BY track, title ASC";

    $sessions = $app['dbs']['mysql_read']->fetchAll($sqlStatement);

    return $app['twig']->render('sessions.html.twig', array(
        'nav' => $navigation,
        'active' => 'Home',
        'sessions' => $sessions
    ));
});

$app->get("/speakers", function (Silex\Application $app) use ($navigation) {

    $sql = "SELECT fname, lname, bio FROM c4p WHERE status = 'accepted' GROUP BY fname, lname ORDER BY fname, last ASC";

    $speakers = $app['dbs']['mysql_read']->fetchAll($sql);

    return $app['twig']->render('speakers.html.twig', array(
        'nav' => $navigation,
        'active' => 'Home',
        'speakers' => $speakers
    ));
});
$app->get("/sponsorList", function (Silex\Application $app) use ($navigation) {

    return $app['twig']->render('sponsor_list.html.twig', array(
        'nav' => $navigation,
        'active' => 'Sponsors'
    ));
});
$app->get("/sponsorCall", function (Silex\Application $app) use ($navigation) {

    return $app['twig']->render('sponsor_call.html.twig', array(
        'nav' => $navigation,
        'active' => 'Sponsors'
    ));
});
$app->get("/venue", function (Silex\Application $app) use ($navigation) {

    return $app['twig']->render('venue.html.twig', array(
        'nav' => $navigation,
        'active' => 'Venue'
    ));
});
$app->get("/contact", function (Silex\Application $app) use ($navigation) {

    return $app['twig']->render('contact.html.twig', array(
        'nav' => $navigation,
        'active' => 'Contact'
    ));
});


$app->run();
