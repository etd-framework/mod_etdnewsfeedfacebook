<?php
/**
 * @package		Module ETD Newsfeed Facebook - ETD Solutions
 *
 * @version		1.0.1
 * @copyright	Copyright (C) 2018 ETD Solutions, SARL Etudoo. Tous droits réservés.
 * @license		http://etd-solutions.com/licence
 * @author		ETD Solutions http://etd-solutions.com
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

require_once __DIR__ . '/../vendor/autoload.php'; // change path as needed

$config = [
    'app_id' => $params->get('app_id'),
    'app_secret' => $params->get('app_secret'),
    'default_graph_version' => 'v2.12',
    //'default_access_token' => '{access-token}', // optional
];

$fb = new \Facebook\Facebook($config);

$access_token = (new Facebook\FacebookApp($config['app_id'], $config['app_secret']))->getAccessToken();

try {
    // Returns a `Facebook\FacebookResponse` object
    $response = $fb->get('/' . $params->get('page_id') . '/feed?limit=' . $params->get('feed_limit', '3'), $access_token);

} catch(Facebook\Exceptions\FacebookResponseException $e) {
    echo 'Graph returned an error: ' . $e->getMessage();
    exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
    echo 'Facebook SDK returned an error: ' . $e->getMessage();
    exit;
}

$posts = json_decode($response->getBody())->data;

JFactory::getDocument()->addStyleDeclaration("
/* Cet élément block le script injecté dans l'iframe. On le cache */
#fb-root { display: none; }

/* on redéfini la taille du contenu */
.fb_iframe_widget, .fb_iframe_widget span, .fb_iframe_widget span iframe { width: 100% !important; }

.fb-post { margin-bottom: 15px; }
");
?>
<div id="fb-root"></div>
<script>(function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = 'https://connect.facebook.net/fr_FR/sdk.js#xfbml=1&version=v2.12&appId=<?php echo $config['app_id']; ?>&autoLogAppEvents=1';
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));</script>
<?php foreach ($posts as $i => $post) : ?>
    <div class="col-xs-12 col-sm-6 col-md-4">
        <div class="fb-post" data-href="https://www.facebook.com/<?php echo $params->get('page_id'); ?>/posts/<?php echo substr($post->id, strpos($post->id, '_') + 1); ?>/" data-show-text="true"></div>
    </div>
    <div class="clearfix visible-xs"></div>
    <?php if(($i + 1) % 2 == 0) : ?>
        <div class="clearfix visible-sm"></div>
    <?php endif ; ?>
    <?php if(($i + 1) % 3 == 0) : ?>
        <div class="clearfix visible-md visible-lg"></div>
    <?php endif ; ?>
<?php endforeach; ?>
