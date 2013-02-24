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
    array('title' => 'Schedule', 'link' => '/schedule'),
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
        'active' => 'Home',
        'hideRightBar' => false
    ));
});

$app->get("/badge", function (Silex\Application $app) use ($navigation) {
    return $app['twig']->render('badges.html.twig', array(
        'nav' => $navigation,
        'active' => 'Home',
        'hideRightBar' => false
    ));
});

$app->get("/banners", function (Silex\Application $app) use ($navigation) {
    return $app['twig']->render('banners.html.twig', array(
        'nav' => $navigation,
        'active' => 'Home',
        'hideRightBar' => false
    ));
});

$app->get("/register", function (Silex\Application $app) use ($navigation) {
    return $app['twig']->render('register.html.twig', array(
        'nav' => $navigation,
        'active' => 'Register',
        'hideRightBar' => false
    ));
});

$app->get("/sessions", function (Silex\Application $app) use ($navigation) {

    joindInApi('events/1090/talks','', array());

    $sqlStatement = "SELECT id, title, fname, lname, summary FROM c4p WHERE status = 'accepted' ORDER BY track, title ASC";

    $sessions = $app['dbs']['mysql_read']->fetchAll($sqlStatement);

    return $app['twig']->render('sessions.html.twig', array(
        'nav' => $navigation,
        'active' => 'Sessions',
        'sessions' => $sessions,
        'hideRightBar' => false
    ));
});

$app->get("/sessions/{id}", function (Silex\Application $app, $id) use ($navigation) {

    $id = str_replace('_',' ', $id);

    $sqlStatement = "SELECT id, title, fname, lname, summary FROM c4p WHERE status = 'accepted' and title = ? ORDER BY track, title ASC";

    $session = $app['dbs']['mysql_read']->fetchAssoc($sqlStatement, array((string) $id));

    return $app['twig']->render('session.html.twig', array(
        'nav' => $navigation,
        'active' => 'Sessions',
        'session' => $session,
        'hideRightBar' => false
    ));
});

$app->get("/schedule", function (Silex\Application $app) use ($navigation) {

    $sql = "SELECT c4p.title, c4p.fname, c4p.lname, t.name FROM c4p LEFT JOIN tracks as t on t.trackId = c4p.track WHERE status = 'accepted' and t.name <> 'Keynotes' ORDER BY session_order ASC";

    $sessions = $app['dbs']['mysql_read']->fetchAll($sql);

    $day1 = array_slice($sessions, 0, 20);
    $day2 = array_slice($sessions, 20);

    return $app['twig']->render('schedule.html.twig', array(
        'nav' => $navigation,
        'active' => 'Schedule',
        'sessions' => $day1,
        'day2' => $day2,
        'hideRightBar' => true
    ));
});

$app->get("/speakers", function (Silex\Application $app) use ($navigation) {

    $sql = "SELECT id, title, fname, lname, bio FROM c4p WHERE status = 'accepted' GROUP BY fname, lname ORDER BY fname, lname ASC";

    $speakers = $app['dbs']['mysql_read']->fetchAll($sql);

    return $app['twig']->render('speakers.html.twig', array(
        'nav' => $navigation,
        'active' => 'Speakers',
        'speakers' => $speakers,
        'hideRightBar' => false
    ));
});

$app->get("/sponsorList", function (Silex\Application $app) use ($navigation) {

    return $app['twig']->render('sponsor_list.html.twig', array(
        'nav' => $navigation,
        'active' => 'Sponsors',
        'hideRightBar' => false
    ));
});

$app->get("/sponsorCall", function (Silex\Application $app) use ($navigation) {

    return $app['twig']->render('sponsor_call.html.twig', array(
        'nav' => $navigation,
        'active' => 'Sponsors',
        'hideRightBar' => false
    ));
});

$app->get("/venue", function (Silex\Application $app) use ($navigation) {

    return $app['twig']->render('venue.html.twig', array(
        'nav' => $navigation,
        'active' => 'Venue',
        'hideRightBar' => false
    ));
});

$app->get("/contact", function (Silex\Application $app) use ($navigation) {

    return $app['twig']->render('contact.html.twig', array(
        'nav' => $navigation,
        'active' => 'Contact',
        'hideRightBar' => false
    ));
});


$app->run();

function joindInApi($endPoint, $action, array $params = array())
{
        $requestData = array(
            'request' => array(
                'action' => array(
                    'type' => $action,
                    'data' => $params
                )
            )
        );
        
        $options = array(
            CURLOPT_RETURNTRANSFER => TRUE,     // return web page
            CURLOPT_HEADER         => FALSE,    // don't return headers
            CURLOPT_FOLLOWLOCATION => TRUE,     // follow redirects
            CURLOPT_ENCODING       => '',       // handle all encodings
            CURLOPT_USERAGENT      => 'DAVE!',  // who am i
            CURLOPT_AUTOREFERER    => TRUE,     // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
            CURLOPT_TIMEOUT        => 120,      // timeout on response
            CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
            CURLOPT_HTTPHEADER     => array('Content-Type: application/json'),
            //CURLOPT_POSTFIELDS     => json_encode($requestData)
        );

        $ch = curl_init('http://api.joind.in/v2.1/' . $endPoint);
        curl_setopt_array($ch, $options);
        $content = curl_exec($ch);
        $err = curl_errno($ch);
        $errmsg = curl_error($ch);
        $header = curl_getinfo($ch);
        curl_close($ch);
        return json_decode($content, TRUE);
}
