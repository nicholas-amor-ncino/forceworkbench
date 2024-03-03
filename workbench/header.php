<!DOCTYPE html>
<html>
    <head>
        <?php
        if (getenv('GA_TRACKING_ID') !== false) {
            print "<script async src=\"https://www.googletagmanager.com/gtag/js?id=". getenv('GA_TRACKING_ID') . "\"></script>";
            print "<script>";
            print "  window.dataLayer = window.dataLayer || [];";
            print "  function gtag(){dataLayer.push(arguments);}";
            print "  gtag('js', new Date());";
            print "  gtag('config', 'UA-119670592-1');";
            print "</script>";
        }
        ?>
        <?php
        if (getenv('PINGDOM_RUM') !== false) {
            print "<script src=\"" . getenv('PINGDOM_RUM') . "\" async></script>";
        }
        ?>
        <?php
        if (getenv('SENTRY_CLIENT_DSN') !== false) {
            print "<script src=\"https://cdn.ravenjs.com/3.25.2/raven.min.js\" crossorigin=\"anonymous\"></script>";
            print "<script>Raven.config(\"" . getenv('SENTRY_CLIENT_DSN') . "\").install()</script>";
        }
        ?>
        <meta http-equiv="Content-Language" content="UTF-8" />
        <meta http-equiv="Content-Type" content="text/xhtml; charset=UTF-8" />

        <link rel="shortcut icon" href="<?= getPathToStaticResource('/images/favicon.ico'); ?>" />

        <link rel="stylesheet" type="text/css" href="<?= getPathToStaticResource('/style/main.css'); ?>" />
        <link rel="stylesheet" type="text/css" href="<?= getPathToStaticResource('/style/pro_dropdown.css'); ?>" />
        <link rel="stylesheet" type="text/css" href="<?= getPathToStaticResource('/style/simpletree.css'); ?>" />

        <title><?php
        $myPage = getMyPage();
        print $myPage->showTitle ? $myPage->title . ": Workbench" : "Workbench";
        ?></title>
        <script type='text/javascript'>var getPathToStaticResource = <?php
        print getPathToStaticResourceAsJsFunction();
        ?>;</script>
        
		<script type="text/javascript" src="<?= getPathToStaticResource('/script/pro_dropdown.js'); ?>"></script>
    </head>
<body>

<?php
if (WorkbenchConfig::get()->isConfigured("displayLiveMaintenanceMessage")) {
    print "<div style='background-color: orange; width: 100%; padding: 2px; font-size: 8pt; font-weight: bold;'>" .
              "Workbench is currently undergoing maintenance. The service may be intermittently unavailable during this time.</div><br/>";
}


// if async SOQL UI is not set, do not display it in the menu
if (!WorkbenchConfig::get()->value("allowAsyncSoqlUI"))  {
    $asyncSOQLpage = $GLOBALS["MENUS"]['Queries']['asyncSOQL.php'];
    $asyncSOQLpage->onNavBar = false;
}

// If the API version is not correct, do not display Async SOQL in the menu
if (WorkbenchContext::isEstablished() && !WorkbenchContext::get()->isApiVersionAtLeast(36.0)) {
    $asyncSOQLpage = $GLOBALS["MENUS"]['Queries']['asyncSOQL.php'];
    $asyncSOQLpage->onNavBar = false;
}

//check for latest version
function strip_seps($haystack) {
    foreach (array(' ', '_', '-') as $n) {
        $haystack = str_replace($n, "", $haystack);
    }
    return $haystack;
}

if (WorkbenchConfig::get()->value("checkForLatestVersion") && extension_loaded('curl') && (isset($_GET['autoLogin']) || 'login.php'==basename($_SERVER['PHP_SELF']))) {
    try {
        $ch = curl_init();
        curl_setopt ($ch, CURLOPT_URL, 'https://api.github.com/repos/forceworkbench/forceworkbench/tags');
        curl_setopt ($ch, CURLOPT_USERAGENT, getWorkbenchUserAgent());
        curl_setopt($ch, CURLOPT_TIMEOUT, 2);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $tagsResponse = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);
        if ($tagsResponse === false || $info['http_code'] != 200) {
            throw new Exception("Could not access GitHub tags");
        }

        $tags = json_decode($tagsResponse);

        $betaTagNames = array();
        $gaTagNames = array();
        foreach ($tags as $tag) {
            if (preg_match('/^[0-9]+.[0-9]+/',$tag->name) === 0) {
                continue;
            } else if (stristr($tag->name, 'beta') ) {
                $betaTagNames[] = $tag->name;
        } else {
                $gaTagNames[] = $tag->name;
            }
        }
        rsort($betaTagNames);
        rsort($gaTagNames);

        $latestBetaVersion = strip_seps($betaTagNames[0]);
        $latestGaVersion = strip_seps($gaTagNames[0]);
        $currentVersion = strip_seps($GLOBALS["WORKBENCH_VERSION"]);

        if (stristr($currentVersion, 'beta') && !stristr($latestBetaVersion, $latestGaVersion)) {
            $latestChannelVersion = $latestBetaVersion;
        } else {
            $latestChannelVersion = $latestGaVersion;
            }

        if ($latestChannelVersion > $currentVersion) {
            print "<div style='background-color: #EAE9E4; width: 100%; padding: 2px;'>" .
                    "<a href='https://github.com/forceworkbench/forceworkbench/tags' target='_blank' " .
                        "style='font-size: 8pt; font-weight: bold; color: #0046ad;'>" .
                        "A newer version of Workbench is available for download</a>" .
                  "</div><br/>";
        }
    } catch (Exception $e) {
        //do nothing
    }
}
?>

<nav class="navbar">
    <div class="container">
    <span class="preload1"></span>
    <span class="preload2"></span>
    <div class="navbar-brand">
        <a class="navbar-item" href="/">
            <img src="<?= getPathToStaticResource('/images/workbench-3-cubed.png') ?>" />
        </a>
    </div>
    <div class="navbar-menu">
    <?php foreach ($GLOBALS["MENUS"] as $menu => $pages):?>
        <?php 
        if (isReadOnlyMode() && $menu == "Data") { //special-case for Data menu, since all read-only
            continue;
        }
        ?>
        <div class="navbar-item has-dropdown is-hoverable">
            <a class='navbar-link'><?= $menu ?></a>

            <div class='navbar-dropdown'>
            <?php foreach ($pages as $href => $page): ?>
                <?php
                if (
                    !$page->onNavBar || 
                    (!isLoggedIn() && $page->requiresSfdcSession) ||
                    (isLoggedIn() && $page->title == 'Login') ||
                    (!$page->isReadOnly && isReadOnlyMode())
                ) {
                    continue;
                }
                ?>
                <a class="navbar-item" href="<?= $href ?>" title="<?= $page->desc ?>" target="<?= $page->window ?>">
                    <?= $page->title ?>
                </a>
            <?php endforeach; ?>
            </div>
        </div>
    
        <?php 
        if(!isLoggedIn() || !termsOk()) break; //only show first "Workbench" menu in these cases
        ?>
    <?php endforeach; ?>
    </ul>
    </div>
</nav>

<div id="container" class="container">

<?php
if (!termsOk() && $myPage->requiresSfdcSession) {
    ?>
    <div style="margin-left: 95px; margin-top: 10px;">
        <form method="POST" action="">
            <input type="checkbox" id="termsAccepted" name="termsAccepted"/>
            <label for="termsAccepted"><a href="terms.php" target="_blank">I agree to the terms of service</a></label>
            <input type="submit" value="Continue" style="margin-left: 10px; "/>
        </form>
    </div>
   <?php
    exit;
}

if (isset($GLOBALS['MIGRATION_MESSAGE'])) {
    print "<div class='migrationInfo'>\n";
    print "<p>" . $GLOBALS['MIGRATION_MESSAGE'] . "</p>";
    print "</div>\n";
}

if (isset($errors)) {
    print "<p/>";
    displayError($errors, false, true);
}

if ($myPage->showTitle) {
    print "<h1 id='pageTitle'>" . $myPage->title . "</h1>";
}

?>
