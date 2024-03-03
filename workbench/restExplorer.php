<?php

// $MIGRATION_MESSAGE = "Try the <a href=\"https://github.com/forcedotcom/postman-salesforce-apis\">Salesforce APIs for Postman</a>.";

require_once 'restclient/RestClient.php';
require_once 'controllers/RestExplorerController.php';
require_once 'async/RestExplorerFutureTask.php';
require_once 'session.php';
require_once 'shared.php';

if(!isset($_SESSION['restExplorerController']) || isset($_GET['reset'])) {
    $_SESSION['restExplorerController'] = new RestExplorerController();
}
$c = $_SESSION['restExplorerController'];
$c->onPageLoad();

if ($c->doExecute || $c->autoExec == '1') {
    $f = new RestExplorerFutureTask($c);
    $result = $f->enqueueOrPerform();
}

require_once 'header.php';
?>
<link
	rel="stylesheet" type="text/css"
	href="<?= getPathToStaticResource('/style/restexplorer.css'); ?>" />
<script
	type="text/javascript"
	src="<?= getPathToStaticResource('/script/restexplorer.js'); ?>"></script>

<script
	type="text/javascript"
	src="<?= getPathToStaticResource('/script/simpletreemenu.js'); ?>">
    /***********************************************
    * Dynamic Countdown script- © Dynamic Drive (http://www.dynamicdrive.com)
    * This notice MUST stay intact for legal use
    * Visit http://www.dynamicdrive.com/ for this script and 100s more.
    ***********************************************/
</script>

<?php
if ($c->errors != null) {
    displayError($c->errors);
}
?>

<form action="" method="post">
    <?= getCsrfFormTag() ?>
    <p><em>Choose an HTTP method to perform on the REST API service URI below:</em></p>
    <p>

    <div class="field is-grouped">
        <div class="control">
            <div class="select">
                <select name="requestMethod">
                <?php 
                foreach (RestApiClient::getMethods() as $method) {
                    ?>
                    <option
                        value="<?= $method ?>"
                        <?= $c->requestMethod == $method ? "selected='selected'" : "" ?>
                        onclick="toggleRequestBodyDisplay(this, <?= in_array($method, RestApiClient::getMethodsWithBodies()) ? 'true' : 'false' ?>);" />
                        <?= $method ?>
                    </option>
                    <?php
                }
                ?>
                </select>
            </div>
        </div>
    
        <div class="control">
            <input id="headersButton" type="button" class="button" value="Headers" onclick="toggleRequestHeaders();" />
        </div>
    
        <div class="control">
            <input id="resetButton" type="button" class="button" value="Reset" onclick="resetUrl('<?= WorkbenchContext::get()->getApiVersion(); ?>');" />
        </div>
        
        <div class="control">
            <input id="upButton" type="button" class="button" value="Up" onclick="upUrl();"/>
        </div>

        <div class="control">
            <span id='waitingIndicator' style="display:none">
                <img src='<?= getPathToStaticResource('/images/wait16trans.gif'); ?>'/> Processing...
            </span>
        </div>
    </div>
    
    </p>

    <div class="field">
        <label class="label" for="url">URL</label>
        <div class="control">
            <input id="urlInput" name="url" class="input"
                value="<?= htmlspecialchars($c->url); ?>"
                onKeyPress="if (checkEnter(event)) {document.getElementById('execBtn').click(); return false;}" />
        </div>
    </div>
    <div class="field">
        <div class="control">
            <input id="execBtn" name="doExecute" type="submit" class="button is-primary disableWhileAsyncLoading" value="Execute" />
        </div>
    </div>

    <div id="requestHeaderContainer" style="display: none;">
        <p><strong>Request Headers</strong></p>
        <textarea id="requestHeaders" class="textarea" name="requestHeaders" style="width: 100%; height: 4em; font-family: monospace;"><?= htmlspecialchars($c->requestHeaders); ?></textarea>
        <a id="requestHeadersDefaulter" class="miniLink pseudoLink" style="float: right;"
           onClick="document.getElementById('requestHeaders').value='<?= str_replace("\n", "\\n", $c->getDefaultRequestHeaders()); ?>';">Restore Default Headers</a>
        <br/>
    </div>

    <div id="requestBodyContainer" style="display: <?= in_array($c->requestMethod, RestApiClient::getMethodsWithBodies()) ? 'inline' : 'none';?>;">
        <p>
            <strong>Request Body</strong>
        </p>
        <textarea class="textarea" name="requestBody" style="width: 100%; height: 10em; font-family: courier, monospace;"><?= htmlspecialchars($c->requestBody); ?></textarea>
        <br/>
    </div>
</form>

<p />

<div id="restExplorerResultsContainer">
<?php
if (isset($c->autoExec) && !$c->autoExec) {
    displayError("This URI needs to be completed before executing. " .
                       "For example, it may need a merge field populated (e.g. {ID}) or a query string appended (e.g. ?q=)");
}

if (isset($result)) {
    echo $result;
}
?>
</div>

<script type="text/javascript">
    var restExplorer = function() {
        function showWaitingIndicator() {
            document.getElementById('waitingIndicator').style.display = 'inline';
        }

        if (<?= !hasRedis() ?>) {
            bindEvent(document.getElementById('execBtn'), 'click', showWaitingIndicator);

            var linkables = document.getElementById('restExplorerResultsContainer').getElementsByClassName('RestLinkable');
            for (var link in linkables) {
                bindEvent(linkables[link], 'click', showWaitingIndicator);
            }
        }
    }();
</script>

<?php
require_once 'footer.php';
?>
